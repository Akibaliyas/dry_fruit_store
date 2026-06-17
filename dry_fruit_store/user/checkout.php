<?php
require_once '../includes/config.php';

// Only logged-in users (non-admin) can access checkout
if(!isLoggedIn() || isAdmin()) {
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];

// Get cart items - EXPLICITLY select product_id from cart table
$cart_query = "SELECT c.id as cart_id, c.product_id, c.quantity, 
                      p.id as product_db_id, p.name, p.price, p.stock_quantity, p.image_path
               FROM cart c 
               JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = '$user_id'";
$cart_result = mysqli_query($conn, $cart_query);
$cart_items = [];

$total = 0;
while($item = mysqli_fetch_assoc($cart_result)) {
    // Check stock availability (use stock_quantity from products)
    if($item['quantity'] > $item['stock_quantity']) {
        $_SESSION['error'] = "Some items in your cart exceed available stock. Please update quantities.";
        redirect('cart.php');
    }
    $subtotal = $item['price'] * $item['quantity'];
    $total += $subtotal;
    $cart_items[] = $item;
}

// If cart is empty, redirect back
if(empty($cart_items)) {
    redirect('cart.php');
}

$error = ''; // Initialize error variable

// Process order form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = sanitize($_POST['address']);
    $payment_method = sanitize($_POST['payment_method']);
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // 1. Insert order
        $order_query = "INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, status) 
                        VALUES ('$user_id', '$total', '$address', '$payment_method', 'pending')";
        if(!mysqli_query($conn, $order_query)) {
            throw new Exception("Failed to create order: " . mysqli_error($conn));
        }
        $order_id = mysqli_insert_id($conn);
        
        // 2. Insert order items and update stock
        foreach($cart_items as $item) {
            $product_id = $item['product_id']; // Now this exists!
            $quantity = $item['quantity'];
            $price = $item['price'];
            
            // Insert order item
            $item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                           VALUES ('$order_id', '$product_id', '$quantity', '$price')";
            if(!mysqli_query($conn, $item_query)) {
                throw new Exception("Failed to add order item: " . mysqli_error($conn));
            }
            
            // Update stock
            $update_stock = "UPDATE products SET stock_quantity = stock_quantity - $quantity 
                             WHERE id = '$product_id'";
            if(!mysqli_query($conn, $update_stock)) {
                throw new Exception("Failed to update stock: " . mysqli_error($conn));
            }
        }
        
        // 3. Clear user's cart
        if(!mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'")) {
            throw new Exception("Failed to clear cart: " . mysqli_error($conn));
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Set success message and redirect to orders page
        $_SESSION['success'] = "Order placed successfully! Your order ID is #$order_id";
        redirect("orders.php");
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = "Failed to place order. Error: " . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="form-container" style="max-width: 800px;">
        <h2 style="text-align: center; margin-bottom: 1rem;">Checkout</h2>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <!-- Order Summary -->
        <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem;">
            <h3>Order Summary</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <th style="text-align: left; padding: 8px;">Product</th>
                        <th style="text-align: left; padding: 8px;">Quantity</th>
                        <th style="text-align: right; padding: 8px;">Price</th>
                        <th style="text-align: right; padding: 8px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($cart_items as $item): ?>
                        <?php $subtotal = $item['price'] * $item['quantity']; ?>
                        <tr>
                            <td style="padding: 8px;"><?php echo htmlspecialchars($item['name']); ?></td>
                            <td style="padding: 8px;"><?php echo $item['quantity']; ?></td>
                            <td style="text-align: right; padding: 8px;"><?php echo CURRENCY; ?> <?php echo number_format($item['price'], 2); ?></td>
                            <td style="text-align: right; padding: 8px;"><?php echo CURRENCY; ?> <?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="border-top: 2px solid #ddd;">
                        <td colspan="3" style="text-align: right; padding: 8px; font-weight: bold;">Total:</td>
                        <td style="text-align: right; padding: 8px; font-weight: bold; font-size: 1.2rem;">
                            <?php echo CURRENCY; ?> <?php echo number_format($total, 2); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <!-- Checkout Form -->
        <form method="POST" action="">
            <div class="form-group">
                <label>Shipping Address *</label>
                <textarea name="address" rows="3" required placeholder="Enter your complete shipping address"><?php echo htmlspecialchars($_SESSION['address'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Payment Method *</label>
                <select name="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="cod">Cash on Delivery (COD)</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="easypaisa">EasyPaisa</option>
                    <option value="jazzcash">JazzCash</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-success" style="flex: 1;">Place Order</button>
                <a href="cart.php" class="btn btn-primary" style="background: #666; text-align: center; flex: 1;">Back to Cart</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>