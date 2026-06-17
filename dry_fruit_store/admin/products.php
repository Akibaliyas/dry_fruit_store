<?php
require_once '../includes/config.php';

if(!isAdmin()) {
    redirect('../login.php');
}

// Handle product deletion
if(isset($_GET['delete'])) {
    $id = sanitize($_GET['delete']);
    $result = mysqli_query($conn, "SELECT image_path FROM products WHERE id='$id'");
    $product = mysqli_fetch_assoc($result);
    if($product['image_path'] && file_exists('../assets/uploads/' . $product['image_path'])) {
        unlink('../assets/uploads/' . $product['image_path']);
    }
    mysqli_query($conn, "DELETE FROM products WHERE id='$id'");
    redirect('products.php');
}

$products = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM products p 
                                 LEFT JOIN categories c ON p.category_id = c.id 
                                 ORDER BY p.created_at DESC");

include '../includes/header.php';
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>Manage Products</h2>
        <a href="add_product.php" class="btn btn-primary">Add New Product</a>
    </div>
    
    <div style="background: white; border-radius: 15px; overflow-x: auto;">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($product = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td>
                            <?php if($product['image_path']): ?>
                                <img src="../assets/uploads/<?php echo $product['image_path']; ?>" 
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; background: #ddd;"></div>
                            <?php endif; ?>
                         </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo $product['category_name']; ?></td>
                        <td>₹<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo $product['stock_quantity']; ?></td>
                        <td><?php echo $product['status']; ?></td>
                        <td>
                            <!-- ✅ CORRECT EDIT LINK -->
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary" style="padding: 0.25rem 0.5rem;">Edit</a>
                            <a href="?delete=<?php echo $product['id']; ?>" class="btn btn-danger" style="padding: 0.25rem 0.5rem;" onclick="return confirm('Are you sure?')">Delete</a>
                         </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>