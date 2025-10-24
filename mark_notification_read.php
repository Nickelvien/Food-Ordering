<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$notification_id = isset($_POST['notification_id']) ? intval($_POST['notification_id']) : 0;

if ($notification_id > 0) {
    $update_query = "UPDATE notifications SET is_read = 1 WHERE id = $notification_id";
    
    if (mysqli_query($conn, $update_query)) {
        echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
}
?>
