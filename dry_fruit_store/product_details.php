<?php
require_once 'includes/config.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($product_id <= 0) {
    header("Location: shop.php");
    exit();
}

// Fetch product details with category name
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.id = $product_id AND p.status = 'active'";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if(!$product) {
    // Product not found or inactive
    header("Location: shop.php");
    exit();
}

include 'includes/header.php';
?>

<div class="container">
    <div style="background: white; border-radius: 15px; overflow: hidden; display: flex; flex-wrap: wrap; margin: 2rem 0; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        
        <!-- Product Image Section -->
        <div style="flex: 1; min-width: 300px; padding: 2rem; text-align: center; background: #f8f9fa;">
            <?php 
            $image_path = 'assets/uploads/' . $product['image_path'];
            if(!file_exists($image_path) || empty($product['image_path'])) {
                $image_path = 'assets/uploads/placeholder.jpg';
            }
            ?>
            <img src="<?php echo $image_path; ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                 style="max-width: 100%; max-height: 400px; object-fit: contain; border-radius: 10px;">
        </div>
        
        <!-- Product Details Section -->
        <div style="flex: 1; min-width: 300px; padding: 2rem;">
            <h1 style="color: #333; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p style="color: #667eea; font-size: 1.1rem; margin-bottom: 0.5rem;">
                Category: <?php echo htmlspecialchars($product['category_name']); ?>
            </p>
            <p style="font-size: 2.2rem; color: #ff4757; font-weight: bold; margin: 1rem 0;">
                ₹<?php echo number_format($product['price'], 2); ?>
            </p>
            <div style="margin: 1rem 0; padding: 1rem 0; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
                <strong>Stock Status:</strong> 
                <?php if($product['stock_quantity'] > 0): ?>
                    <span style="color: #00b894;">In Stock (<?php echo $product['stock_quantity']; ?> units available)</span>
                <?php else: ?>
                    <span style="color: #ff4757;">Out of Stock</span>
                <?php endif; ?>
            </div>
            <div style="margin: 1.5rem 0;">
                <h3>Description</h3>
                <p style="color: #666; line-height: 1.6;">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </p>
            </div>
            
            <div style="margin-top: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                <?php if(isLoggedIn() && !isAdmin()): ?>
                    <?php if($product['stock_quantity'] > 0): ?>
                        <a href="user/cart.php?add=<?php echo $product['id']; ?>" class="btn btn-primary" style="padding: 0.8rem 2rem;">Add to Cart</a>
                    <?php else: ?>
                        <button class="btn btn-danger" disabled style="padding: 0.8rem 2rem;">Out of Stock</button>
                    <?php endif; ?>
                <?php elseif(!isLoggedIn()): ?>
                    <a href="login.php" class="btn btn-primary" style="padding: 0.8rem 2rem;">Login to Purchase</a>
                <?php endif; ?>
                <a href="shop.php" class="btn btn-primary" style="background: #666; padding: 0.8rem 2rem;">Continue Shopping</a>
            </div>
        </div>
    </div>
    
    <!-- You can add related products section here if needed -->
</div>

<?php include 'includes/footer.php'; ?>