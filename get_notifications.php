<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'customer';

// Get unread count
if ($role === 'admin') {
    // Admin sees all new order notifications
    $count_query = "SELECT COUNT(*) as count FROM notifications 
                    WHERE type = 'new_order' AND is_read = 0";
} else {
    // Customers see their own notifications
    $count_query = "SELECT COUNT(*) as count FROM notifications 
                    WHERE user_id = $user_id AND is_read = 0";
}

$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$unread_count = $count_row['count'];

// Get recent notifications
if ($role === 'admin') {
    $notifications_query = "SELECT n.*, o.id as order_number, u.full_name as customer_name 
                           FROM notifications n 
                           JOIN orders o ON n.order_id = o.id 
                           JOIN users u ON n.user_id = u.id
                           WHERE n.type = 'new_order'
                           ORDER BY n.created_at DESC LIMIT 5";
} else {
    $notifications_query = "SELECT n.*, o.id as order_number 
                           FROM notifications n 
                           JOIN orders o ON n.order_id = o.id
                           WHERE n.user_id = $user_id
                           ORDER BY n.created_at DESC LIMIT 5";
}

$notifications_result = mysqli_query($conn, $notifications_query);
$notifications = [];

while ($row = mysqli_fetch_assoc($notifications_result)) {
    $notifications[] = $row;
}

echo json_encode([
    'success' => true,
    'unread_count' => $unread_count,
    'notifications' => $notifications
]);
?>
