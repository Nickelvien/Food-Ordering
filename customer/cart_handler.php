<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

switch ($action) {
    case 'add':
        // Accept both 'item_id' and 'food_id' for compatibility
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        if ($item_id == 0) {
            $item_id = isset($_POST['food_id']) ? (int)$_POST['food_id'] : 0;
        }
        
        if ($item_id > 0) {
            // Check if item exists and is available
            $query = "SELECT * FROM food_items WHERE id = $item_id AND is_available = 1";
            $result = mysqli_query($conn, $query);
            
            if (mysqli_num_rows($result) > 0) {
                if (isset($_SESSION['cart'][$item_id])) {
                    $_SESSION['cart'][$item_id]++;
                } else {
                    $_SESSION['cart'][$item_id] = 1;
                }
                echo json_encode(['success' => true, 'message' => 'Item added to cart', 'cart_count' => get_cart_count()]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Item not available']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
        }
        break;
        
    case 'update':
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        
        if ($item_id > 0 && $quantity > 0) {
            $_SESSION['cart'][$item_id] = $quantity;
            echo json_encode(['success' => true, 'message' => 'Cart updated', 'cart_total' => get_cart_total()]);
        } elseif ($item_id > 0 && $quantity == 0) {
            unset($_SESSION['cart'][$item_id]);
            echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
        }
        break;
        
    case 'remove':
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        
        if ($item_id > 0 && isset($_SESSION['cart'][$item_id])) {
            unset($_SESSION['cart'][$item_id]);
            echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not in cart']);
        }
        break;
        
    case 'clear':
        $_SESSION['cart'] = [];
        echo json_encode(['success' => true, 'message' => 'Cart cleared']);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
