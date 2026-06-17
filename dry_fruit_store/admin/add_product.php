<?php
require_once '../includes/config.php';

if(!isAdmin()) {
    redirect('../login.php');
}

$error = '';
$success = '';

// Get categories
$categories = mysqli_query($conn, "SELECT * FROM categories WHERE status='active'");

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $category_id = sanitize($_POST['category_id']);
    $description = sanitize($_POST['description']);
    $price = sanitize($_POST['price']);
    $stock_quantity = sanitize($_POST['stock_quantity']);
    
    // Handle image upload
    $image_path = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            $image_path = time() . '_' . $filename;
            $upload_path = '../assets/uploads/' . $image_path;
            
            if(!is_dir('../assets/uploads')) {
                mkdir('../assets/uploads', 0777, true);
            }
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Image uploaded successfully
            } else {
                $error = 'Failed to upload image';
            }
        } else {
            $error = 'Invalid file type. Allowed: jpg, jpeg, png, gif';
        }
    }
    
    if(!$error) {
        $query = "INSERT INTO products (name, category_id, description, price, stock_quantity, image_path) 
                  VALUES ('$name', '$category_id', '$description', '$price', '$stock_quantity', '$image_path')";
        
        if(mysqli_query($conn, $query)) {
            $success = 'Product added successfully!';
        } else {
            $error = 'Failed to add product';
        }
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="form-container" style="max-width: 800px;">
        <h2 style="text-align: center;">Add New Product</h2>
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name*</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Category*</label>
                <select name="category_id" required>
                    <option value="">Select Category</option>
                    <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Price* (₹)</label>
                <input type="number" step="0.01" name="price" required>
            </div>
            <div class="form-group">
                <label>Stock Quantity*</label>
                <input type="number" name="stock_quantity" required>
            </div>
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="image" accept="image/*">
                <small>Allowed: jpg, jpeg, png, gif</small>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Add Product</button>
            <a href="products.php" style="display: block; text-align: center; margin-top: 1rem;">Back to Products</a>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>