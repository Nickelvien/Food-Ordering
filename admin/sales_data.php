<?php
require_once '../db.php';
header('Content-Type: application/json');

// Last 7 days sales data
$labels = [];
$values = [];

for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-{$i} days"));
    $labels[] = date('D', strtotime($day)); // Mon, Tue, etc.
    
    $res = mysqli_query($conn, "SELECT IFNULL(SUM(total_amount), 0) as total 
                                FROM orders 
                                WHERE DATE(created_at) = '{$day}' 
                                AND status NOT IN ('cancelled', 'pending')");
    $r = mysqli_fetch_assoc($res);
    $values[] = (float)$r['total'];
}

echo json_encode([
    'labels' => $labels,
    'values' => $values
]);
?>
