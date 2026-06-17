<?php
require_once '../includes/config.php';

if(!isAdmin()) {
    redirect('../login.php');
}

$error = '';
$success = '';
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch product details
$query = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if(!$product) {
    redirect('products.php');
}

// Get categories
$categories = mysqli_query($conn, "SELECT * FROM categories WHERE status='active'");

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $category_id = sanitize($_POST['category_id']);
    $description = sanitize($_POST['description']);
    $price = sanitize($_POST['price']);
    $stock_quantity = sanitize($_POST['stock_quantity']);
    $status = sanitize($_POST['status']);
    
    // Image upload handling with directory creation
    $image_path = $product['image_path'];
    $upload_dir = '../assets/uploads/';
    
    // Create directory if not exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            // Delete old image
            if($image_path && file_exists($upload_dir . $image_path)) {
                unlink($upload_dir . $image_path);
            }
            
            $new_filename = time() . '_' . $filename;
            $upload_path = $upload_dir . $new_filename;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = $new_filename;
            } else {
                $error = 'Failed to upload new image. Check folder permissions.';
            }
        } else {
            $error = 'Invalid file type. Allowed: jpg, jpeg, png, gif';
        }
    }
    
    if(!$error) {
        $update_query = "UPDATE products SET 
                         name = '$name',
                         category_id = '$category_id',
                         description = '$description',
                         price = '$price',
                         stock_quantity = '$stock_quantity',
                         image_path = '$image_path',
                         status = '$status'
                         WHERE id = $product_id";
        
        if(mysqli_query($conn, $update_query)) {
            $success = 'Product updated successfully!';
            // Refresh product data
            $result = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id");
            $product = mysqli_fetch_assoc($result);
        } else {
            $error = 'Failed to update product: ' . mysqli_error($conn);
        }
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="form-container" style="max-width: 800px;">
        <h2 style="text-align: center;">Edit Product</h2>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name*</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Category*</label>
                <select name="category_id" required>
                    <option value="">Select Category</option>
                    <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo $cat['id']; ?>" 
                            <?php echo ($cat['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Price* (₹)</label>
                <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Stock Quantity*</label>
                <input type="number" name="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="active" <?php echo ($product['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($product['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Current Image</label><br>
                <?php if($product['image_path']): ?>
                    <img src="../assets/uploads/<?php echo $product['image_path']; ?>" 
                         style="max-width: 200px; margin: 10px 0;">
                <?php else: ?>
                    <p>No image uploaded</p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Change Image (optional)</label>
                <input type="file" name="image" accept="image/*">
                <small>Leave empty to keep current image. Allowed: jpg, jpeg, png, gif</small>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Update Product</button>
            <a href="products.php" style="display: block; text-align: center; margin-top: 1rem;">Back to Products</a>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>