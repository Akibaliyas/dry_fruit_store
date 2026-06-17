<?php
require_once '../includes/config.php';

if(!isAdmin()) {
    redirect('../login.php');
}

// Get statistics
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products"))['count'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='user'"))['count'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status='delivered'"))['total'];

include '../includes/header.php';
?>

<div class="container">
    <h2>Admin Dashboard</h2>
    <p>Welcome, <?php echo $_SESSION['full_name']; ?>!</p>
    
    <div class="admin-stats">
        <div class="stat-card">
            <h3>Total Products</h3>
            <div class="stat-number"><?php echo $total_products; ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Users</h3>
            <div class="stat-number"><?php echo $total_users; ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Orders</h3>
            <div class="stat-number"><?php echo $total_orders; ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Revenue</h3>
            <div class="stat-number">₹<?php echo number_format($total_revenue ?: 0, 2); ?></div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-top: 2rem;">
        <a href="products.php" class="btn btn-primary" style="text-align: center;">Manage Products</a>
        <a href="orders.php" class="btn btn-primary" style="text-align: center;">Manage Orders</a>
        <a href="users.php" class="btn btn-primary" style="text-align: center;">Manage Users</a>
        <a href="../logout.php" class="btn btn-danger" style="text-align: center;">Logout</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>