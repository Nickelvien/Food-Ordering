<?php
require_once '../db.php';
header('Content-Type: application/json');

$count = 0;
if ($conn) {
    $res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM notifications WHERE user_role = 'admin' AND is_read = 0");
    if ($res) {
        $r = mysqli_fetch_assoc($res);
        $count = (int)$r['cnt'];
    }
}

echo json_encode(['count' => $count]);
?>
