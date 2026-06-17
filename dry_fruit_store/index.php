<?php
require_once 'includes/config.php';
include 'includes/header.php';

// Get featured products
$query = "SELECT p.*, c.name as category_name FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.status = 'active' 
          ORDER BY p.created_at DESC LIMIT 6";
$result = mysqli_query($conn, $query);
?>

<div class="container">
    <div style="text-align: center; padding: 3rem 0;">
        <h1 style="color: white; font-size: 3rem;">Welcome to Dry Fruit Store</h1>
        <p style="color: white; font-size: 1.2rem; margin-top: 1rem;">Premium Quality Dry Fruits at Best Prices</p>
    </div>
    
    <div style="background: white; border-radius: 15px; padding: 2rem; margin-bottom: 2rem;">
        <h2 style="text-align: center; margin-bottom: 2rem;">Featured Products</h2>
        <div class="product-grid">
            <?php while($product = mysqli_fetch_assoc($result)): ?>
                <div class="product-card">
                    <img src="<?php echo $base_url; ?>assets/uploads/<?php echo $product['image_path'] ?: 'placeholder.jpg'; ?>" 
                         alt="<?php echo $product['name']; ?>" class="product-image">
                    <div class="product-info">
                        <h3 class="product-title"><?php echo $product['name']; ?></h3>
                        <p class="product-price">₹<?php echo number_format($product['price'], 2); ?></p>
                        <p class="product-description"><?php echo substr($product['description'], 0, 100); ?>...</p>
                        <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>