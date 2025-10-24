<?php
require_once '../db.php';
session_start();
header('Content-Type: application/json');

$uid = $_SESSION['user_id'] ?? 0;
$count = 0;
$latest = null;

if ($uid) {
    // Get count
    $countRes = mysqli_query($conn, "SELECT COUNT(*) AS c FROM notifications 
                                     WHERE user_role='user' AND user_id = ".(int)$uid." AND is_read = 0");
    if ($countRes) {
        $count = (int)mysqli_fetch_assoc($countRes)['c'];
    }
    
    // Get latest unread notification
    $res = mysqli_query($conn, "SELECT id, title, message, created_at 
                                FROM notifications 
                                WHERE user_role='user' AND user_id = ".(int)$uid." AND is_read = 0 
                                ORDER BY created_at DESC LIMIT 1");
    if ($res && mysqli_num_rows($res)) {
        $latest = mysqli_fetch_assoc($res);
    }
}

echo json_encode([
    'count' => $count,
    'latest' => $latest
]);
?>
