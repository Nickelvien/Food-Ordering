<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json; charset=utf-8');

// Basic auth check
if (!is_logged_in() || !is_admin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$order_id = isset($input['order_id']) ? (int)$input['order_id'] : 0;
$new_status = isset($input['new_status']) ? trim($input['new_status']) : '';

if ($order_id <= 0 || $new_status === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

// Allowed transitions (cancel permitted except from delivered)
$allowed_transitions = [
    'pending' => ['confirmed', 'cancelled'],
    'confirmed' => ['preparing', 'cancelled'],
    'preparing' => ['delivered', 'cancelled'],
    'delivered' => [],
    'cancelled' => []
];

// Fetch current status
$stmt = mysqli_prepare($conn, 'SELECT status, user_id, order_number FROM orders WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $order_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
if (!$res || mysqli_num_rows($res) === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}
$order = mysqli_fetch_assoc($res);
$current_status = $order['status'];

// Validate transition
$is_valid = false;
if ($current_status === $new_status) {
    $is_valid = true;
} elseif (isset($allowed_transitions[$current_status]) && in_array($new_status, $allowed_transitions[$current_status])) {
    $is_valid = true;
}

if (!$is_valid) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => "Invalid status transition from $current_status to $new_status"]);
    exit;
}

// Update status
$update = mysqli_prepare($conn, 'UPDATE orders SET status = ? WHERE id = ?');
mysqli_stmt_bind_param($update, 'si', $new_status, $order_id);
if (!mysqli_stmt_execute($update)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database update failed']);
    exit;
}

// Insert notification for customer
$status_display = ucfirst($new_status);
$notif_title = "Order {$order['order_number']} - {$status_display}";
$notif_message = "Your order status has been updated to: {$status_display}";
$ins = mysqli_prepare($conn, 'INSERT INTO notifications (user_role, user_id, title, message, related_id) VALUES (?, ?, ?, ?, ?)');
$user_role = 'user';
mysqli_stmt_bind_param($ins, 'sissi', $user_role, $order['user_id'], $notif_title, $notif_message, $order_id);
mysqli_stmt_execute($ins);

// Respond with success and new status
echo json_encode([
    'success' => true,
    'message' => 'Status updated',
    'new_status' => $new_status
]);
exit;

?>
