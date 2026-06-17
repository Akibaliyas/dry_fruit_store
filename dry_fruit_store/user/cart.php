<?php
require_once '../includes/config.php';

if(!isLoggedIn() || isAdmin()) {
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];

// Add to cart
if(isset($_GET['add'])) {
    $product_id = sanitize($_GET['add']);
    $check = mysqli_query($conn, "SELECT id FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");
    if(mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE user_id='$user_id' AND product_id='$product_id'");
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', 1)");
    }
    redirect('cart.php');
}

// Update quantity
if(isset($_POST['update'])) {
    foreach($_POST['quantity'] as $id => $qty) {
        if($qty <= 0) {
            mysqli_query($conn, "DELETE FROM cart WHERE id='$id' AND user_id='$user_id'");
        } else {
            mysqli_query($conn, "UPDATE cart SET quantity='$qty' WHERE id='$id' AND user_id='$user_id'");
        }
    }
    redirect('cart.php');
}

// Remove from cart
if(isset($_GET['remove'])) {
    $id = sanitize($_GET['remove']);
    mysqli_query($conn, "DELETE FROM cart WHERE id='$id' AND user_id='$user_id'");
    redirect('cart.php');
}

// Get cart items
$cart_items = mysqli_query($conn, "SELECT c.id as cart_id, c.quantity, p.* FROM cart c 
                                    JOIN products p ON c.product_id = p.id 
                                    WHERE c.user_id='$user_id'");

$total = 0;
include '../includes/header.php';
?>

<div class="container">
    <h2>Shopping Cart</h2>
    
    <?php if(mysqli_num_rows($cart_items) == 0): ?>
        <div class="alert alert-error">Your cart is empty!</div>
        <a href="../shop.php" class="btn btn-primary">Continue Shopping</a>
    <?php else: ?>
        <form method="POST" action="">
            <div style="background: white; border-radius: 15px; overflow-x: auto;">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($item = mysqli_fetch_assoc($cart_items)): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td>
                                    <img src="../assets/uploads/<?php echo $item['image_path'] ?: 'placeholder.jpg'; ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php echo $item['name']; ?>
                                </td>
                                <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $item['cart_id']; ?>]" 
                                           value="<?php echo $item['quantity']; ?>" min="0" style="width: 60px;">
                                </td>
                                <td>₹<?php echo number_format($subtotal, 2); ?></td>
                                <td>
                                    <a href="?remove=<?php echo $item['cart_id']; ?>" class="btn btn-danger" 
                                       onclick="return confirm('Remove this item?')">Remove</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                            <td colspan="2"><strong>₹<?php echo number_format($total, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 1rem; display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="submit" name="update" class="btn btn-primary">Update Cart</button>
                <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>