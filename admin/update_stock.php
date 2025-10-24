<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!is_logged_in() || !is_admin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$food_id = isset($data['food_id']) ? (int)$data['food_id'] : 0;
$is_available = isset($data['is_available']) ? (int)$data['is_available'] : 0;

if ($food_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid food item']);
    exit;
}

// Update stock status
$query = "UPDATE food_items SET is_available = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'ii', $is_available, $food_id);

if (mysqli_stmt_execute($stmt)) {
    // Get food item details for notification
    $food_query = "SELECT name FROM food_items WHERE id = ?";
    $food_stmt = mysqli_prepare($conn, $food_query);
    mysqli_stmt_bind_param($food_stmt, 'i', $food_id);
    mysqli_stmt_execute($food_stmt);
    $result = mysqli_stmt_get_result($food_stmt);
    $food = mysqli_fetch_assoc($result);
    
    // Create notification for all users
    $status_text = $is_available ? 'back in stock' : 'out of stock';
    $notif_title = $food['name'] . ' - ' . ucfirst($status_text);
    $notif_message = $food['name'] . ' is now ' . $status_text . '!';
    
    // Insert notification for all regular users
    $notif_query = "INSERT INTO notifications (user_role, title, message, related_id, created_at) 
                    VALUES ('customer', ?, ?, ?, NOW())";
    $notif_stmt = mysqli_prepare($conn, $notif_query);
    mysqli_stmt_bind_param($notif_stmt, 'ssi', $notif_title, $notif_message, $food_id);
    mysqli_stmt_execute($notif_stmt);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Stock status updated successfully',
        'is_available' => $is_available
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update stock status']);
}

mysqli_stmt_close($stmt);
?>
