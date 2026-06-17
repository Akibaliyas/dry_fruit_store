<?php
require_once 'includes/config.php';
include 'includes/header.php';

// Get all products
$query = "SELECT p.*, c.name as category_name FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.status = 'active' 
          ORDER BY p.created_at DESC";
$products = mysqli_query($conn, $query);
?>

<div class="container">
    <h2 style="color: white;">All Products</h2>
    
    <div class="product-grid">
        <?php while($product = mysqli_fetch_assoc($products)): ?>
            <div class="product-card">
                <img src="assets/uploads/<?php echo $product['image_path'] ?: 'placeholder.jpg'; ?>" 
                     alt="<?php echo $product['name']; ?>" class="product-image">
                <div class="product-info">
                    <h3 class="product-title"><?php echo $product['name']; ?></h3>
                    <p class="product-price">₹<?php echo number_format($product['price'], 2); ?></p>
                    <p class="product-description"><?php echo substr($product['description'], 0, 100); ?>...</p>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                        <?php if(isLoggedIn() && !isAdmin()): ?>
                            <a href="user/cart.php?add=<?php echo $product['id']; ?>" class="btn btn-success">Add to Cart</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>