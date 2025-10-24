<?php
/**
 * Database Configuration File
 * This file handles the database connection for the Food Ordering System
 */

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'food_ordering_system');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8mb4 for proper character encoding
mysqli_set_charset($conn, "utf8mb4");

/**
 * Function to sanitize user input to prevent SQL injection
 * @param string $data - The data to sanitize
 * @return string - Sanitized data
 */
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

/**
 * Function to check if user is logged in
 * @return bool - True if logged in, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Function to check if user is admin
 * @return bool - True if admin, false otherwise
 */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Function to redirect to a specific page
 * @param string $url - The URL to redirect to
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Function to generate a unique order number
 * @return string - Unique order number
 */
function generate_order_number() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

/**
 * Function to format price
 * @param float $price - The price to format
 * @return string - Formatted price with currency symbol
 */
function format_price($price) {
    return '₱' . number_format($price, 2);
}

/**
 * Function to get user's cart from session
 * @return array - Cart items
 */
function get_cart() {
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
}

/**
 * Function to calculate cart total
 * @return float - Total cart amount
 */
function get_cart_total() {
    global $conn;
    $cart = get_cart();
    $total = 0;
    
    foreach ($cart as $item_id => $quantity) {
        $query = "SELECT price FROM food_items WHERE id = $item_id";
        $result = mysqli_query($conn, $query);
        if ($row = mysqli_fetch_assoc($result)) {
            $total += $row['price'] * $quantity;
        }
    }
    
    return $total;
}

/**
 * Function to get cart item count
 * @return int - Number of items in cart
 */
function get_cart_count() {
    $cart = get_cart();
    return array_sum($cart);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
