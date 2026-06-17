<?php
require_once 'includes/config.php';

if(isLoggedIn()) {
    redirect(isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php');
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = md5($_POST['password']);
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    
    // Check if username exists
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' OR email='$email'");
    if(mysqli_num_rows($check) > 0) {
        $error = 'Username or email already exists';
    } else {
        $query = "INSERT INTO users (username, email, password, full_name, phone, address) 
                  VALUES ('$username', '$email', '$password', '$full_name', '$phone', '$address')";
        if(mysqli_query($conn, $query)) {
            $success = 'Registration successful! You can now login.';
        } else {
            $error = 'Registration failed. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="form-container">
    <h2 style="text-align: center; margin-bottom: 2rem;">Create New Account</h2>
    <?php if($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label>Username*</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Email*</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Full Name*</label>
            <input type="text" name="full_name" required>
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone">
        </div>
        <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label>Password*</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
    </form>
    <p style="text-align: center; margin-top: 1rem;">Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php include 'includes/footer.php'; ?>