<?php
session_start();
require_once '../db.php';

// Redirect if not logged in
if (!is_logged_in()) {
    redirect('../auth/login.php');
}

// Redirect if cart is empty
$cart = get_cart();
if (empty($cart)) {
    redirect('cart.php');
}

$error = '';
$success = false;

// Get user info
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Calculate totals
$cart_items = [];
$subtotal = 0;

if (!empty($cart)) {
    $item_ids = implode(',', array_keys($cart));
    $query = "SELECT * FROM food_items WHERE id IN ($item_ids) AND is_available = 1";
    $result = mysqli_query($conn, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $row['quantity'] = $cart[$row['id']];
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $subtotal += $row['subtotal'];
        $cart_items[] = $row;
    }
}

$total = $subtotal;

// Process order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    $payment_mode = sanitize_input($_POST['payment_mode']);
    $notes = sanitize_input($_POST['notes']);
    
    if (empty($phone) || empty($address)) {
        $error = 'Phone and address are required';
    } else {
        // Generate order number
        $order_number = generate_order_number();
        
        // Insert order
        $insert_order = "INSERT INTO orders (user_id, order_number, total_amount, delivery_address, phone, payment_mode, notes, status) 
                        VALUES ($user_id, '$order_number', $total, '$address', '$phone', '$payment_mode', '$notes', 'pending')";
        
        if (mysqli_query($conn, $insert_order)) {
            $order_id = mysqli_insert_id($conn);
            
            // Insert order items
            foreach ($cart_items as $item) {
                $item_id = $item['id'];
                $quantity = $item['quantity'];
                $price = $item['price'];
                $item_subtotal = $item['subtotal'];
                
                $insert_item = "INSERT INTO order_items (order_id, food_item_id, quantity, price, subtotal) 
                               VALUES ($order_id, $item_id, $quantity, $price, $item_subtotal)";
                mysqli_query($conn, $insert_item);
            }
            
            // Create admin notification
            $customer_name = mysqli_real_escape_string($conn, $user['full_name']);
            $total_formatted = number_format($total, 2);
            $notif_title = "New Order: {$order_number}";
            $notif_message = "New order from {$customer_name}. Order: {$order_number}. Total: ₱{$total_formatted}";
            $notif_query = "INSERT INTO notifications (user_role, title, message, related_id) 
                           VALUES ('admin', '{$notif_title}', '{$notif_message}', {$order_id})";
            mysqli_query($conn, $notif_query);
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            // Set success flag
            $success = true;
        } else {
            $error = 'Failed to place order. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FoodHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f59e0b',
                        secondary: '#fb923c',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-utensils text-3xl text-primary"></i>
                    <span class="text-2xl font-bold text-gray-800">FoodHub</span>
                </div>
                <a href="cart.php" class="text-gray-700 hover:text-primary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Cart
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>
        
        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <form method="POST" action="checkout.php" id="checkout-form">
                    <!-- Delivery Information -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">
                            <i class="fas fa-map-marker-alt text-primary mr-2"></i>Delivery Information
                        </h2>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Full Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Phone Number *</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Delivery Address *</label>
                            <textarea name="address" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Order Notes (Optional)</label>
                            <textarea name="notes" rows="2" placeholder="Any special instructions..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">
                            <i class="fas fa-credit-card text-primary mr-2"></i>Payment Method
                        </h2>
                        
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-primary">
                                <input type="radio" name="payment_mode" value="cash" checked class="w-4 h-4 text-primary">
                                <i class="fas fa-money-bill-wave text-2xl text-green-600 mx-4"></i>
                                <div>
                                    <p class="font-semibold">Cash on Delivery</p>
                                    <p class="text-sm text-gray-600">Pay when you receive your order</p>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-primary">
                                <input type="radio" name="payment_mode" value="online" class="w-4 h-4 text-primary">
                                <i class="fas fa-credit-card text-2xl text-blue-600 mx-4"></i>
                                <div>
                                    <p class="font-semibold">Online Payment</p>
                                    <p class="text-sm text-gray-600">Pay now (Demo only)</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-primary text-white py-4 rounded-lg font-semibold text-lg hover:bg-secondary transition">
                        <i class="fas fa-check-circle mr-2"></i>Place Order
                    </button>
                </form>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Order Summary</h2>
                    
                    <div class="space-y-3 mb-6 max-h-64 overflow-y-auto">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-700">
                                <?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?>
                            </span>
                            <span class="font-semibold"><?php echo format_price($item['subtotal']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="border-t pt-4">
                        <div class="flex justify-between text-xl font-bold text-gray-800">
                            <span>Total Amount</span>
                            <span class="text-primary"><?php echo format_price($total); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <?php if ($success): ?>
    <div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
            <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-white text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Order Placed Successfully!</h2>
            <p class="text-gray-600 mb-6">Thank you for your order. We'll deliver it soon!</p>
            <a href="orders.php" class="inline-block bg-primary text-white px-6 py-3 rounded-lg hover:bg-secondary transition">
                View My Orders
            </a>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>
