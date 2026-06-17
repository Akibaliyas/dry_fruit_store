<?php
session_start();

// Database configuration
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'dry_fruit_store';
// Currency settings
define('CURRENCY', 'PKR');   // or 'Rs.' or '₨'

// Database configuration
$host = 'localhost';
// ... rest of your config

// Create connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Base URL
$base_url = 'http://localhost/dry_fruit_store/';

// Function to check user login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check admin login
function isAdmin() {
    return isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin';
}

// Function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Function to sanitize input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags($data)));
}

// Function to get product by ID
function getProduct($id) {
    global $conn;
    $id = mysqli_real_escape_string($conn, $id);
    $query = "SELECT p.*, c.name as category_name FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.id = '$id'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Function to get cart count
function getCartCount($user_id) {
    global $conn;
    $query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ? $row['total'] : 0;
}
?>