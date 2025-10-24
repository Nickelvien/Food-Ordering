<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');
echo json_encode(['count' => get_cart_count()]);
?>
