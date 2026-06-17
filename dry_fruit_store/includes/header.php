<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dry Fruit Store - Premium Quality Dry Fruits</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="<?php echo $base_url; ?>" class="logo">Dry<span>Fruit</span> Store</a>
            <ul class="nav-links">
                <li><a href="<?php echo $base_url; ?>">Home</a></li>
                <li><a href="<?php echo $base_url; ?>shop.php">Shop</a></li>
                <?php if(isLoggedIn()): ?>
                    <?php if(isAdmin()): ?>
                        <li><a href="<?php echo $base_url; ?>admin/dashboard.php">Admin Panel</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base_url; ?>user/dashboard.php">My Account</a></li>
                    <?php endif; ?>
                    <li class="cart-icon">
                        <a href="<?php echo $base_url; ?>user/cart.php">Cart 
                            <span class="cart-count"><?php echo getCartCount($_SESSION['user_id']); ?></span>
                        </a>
                    </li>
                    <li><a href="<?php echo $base_url; ?>logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base_url; ?>login.php">Login</a></li>
                    <li><a href="<?php echo $base_url; ?>register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>