<?php
require_once '../includes/config.php';

if(!isLoggedIn() || isAdmin()) {
    redirect('../login.php');
}

// Get user orders
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id='{$_SESSION['user_id']}' ORDER BY order_date DESC");

include '../includes/header.php';
?>

<div class="container">
    <h2>My Dashboard</h2>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 2rem 0;">
        <div class="stat-card">
            <h3>My Orders</h3>
            <div class="stat-number"><?php echo mysqli_num_rows($orders); ?></div>
        </div>
        <div class="stat-card">
            <h3>Cart Items</h3>
            <div class="stat-number"><?php echo getCartCount($_SESSION['user_id']); ?></div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
        <a href="../shop.php" class="btn btn-primary" style="text-align: center;">Continue Shopping</a>
        <a href="cart.php" class="btn btn-primary" style="text-align: center;">View Cart</a>
        <a href="orders.php" class="btn btn-primary" style="text-align: center;">My Orders</a>
        <a href="../logout.php" class="btn btn-danger" style="text-align: center;">Logout</a>
    </div>
    
    <?php if(mysqli_num_rows($orders) > 0): ?>
        <h3 style="margin-top: 2rem;">Recent Orders</h3>
        <div style="background: white; border-radius: 15px; overflow-x: auto;">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = mysqli_fetch_assoc($orders)): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo $order['order_date']; ?></td>
                            <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo ucfirst($order['status']); ?></td>
                            <td><a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-primary" style="padding: 0.25rem 0.5rem;">View</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>