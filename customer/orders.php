<?php
session_start();
require_once '../db.php';

// Redirect if not logged in
if (!is_logged_in()) {
    redirect('../auth/login.php');
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders with user information
$query = "SELECT o.*, u.full_name as customer_name, u.email as customer_email 
          FROM orders o
          JOIN users u ON o.user_id = u.id
          WHERE o.user_id = $user_id 
          ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Gourmet Sentinel</title>
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
    <link rel="stylesheet" href="../assets/css/shared-styles.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-utensils text-3xl text-primary"></i>
                    <span class="text-2xl font-bold text-gray-800">Gourmet Sentinel</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="menu.php" class="text-gray-700 hover:text-primary">Menu</a>
                    <a href="../logout.php" class="text-gray-700 hover:text-primary">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header with Enhanced Design -->
    <div class="page-header text-white py-20">
        <div class="container mx-auto px-4 relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-4">
                        <i class="fas fa-history"></i>
                        <span class="text-sm font-semibold">Order History</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold mb-2">My Orders</h1>
                    <p class="text-xl text-gray-100">Track and view your order history</p>
                </div>
                <div class="hidden md:block">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                        <i class="fas fa-receipt text-4xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        
        <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="space-y-6">
            <?php while ($order = mysqli_fetch_assoc($result)): ?>
            <?php
            // Get order items
            $order_id = $order['id'];
            $items_query = "SELECT oi.*, f.name, f.image 
                           FROM order_items oi 
                           JOIN food_items f ON oi.food_item_id = f.id 
                           WHERE oi.order_id = $order_id";
            $items_result = mysqli_query($conn, $items_query);
            
            // Status colors with modern design
            $status_colors = [
                'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                'confirmed' => 'bg-blue-50 text-blue-700 border-blue-200',
                'preparing' => 'bg-purple-50 text-purple-700 border-purple-200',
                'delivered' => 'bg-green-50 text-green-700 border-green-200',
                'cancelled' => 'bg-red-50 text-red-700 border-red-200'
            ];
            $status_color = $status_colors[$order['status']] ?? 'bg-gray-50 text-gray-700 border-gray-200';
            ?>
            
            <div class="modern-card shadow-primary overflow-hidden card-hover">
                <!-- Order Header with Enhanced Design -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-5 flex flex-col md:flex-row justify-between items-start md:items-center border-b-2 border-primary/20 gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-shopping-bag text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Order #<?php echo htmlspecialchars($order['order_number']); ?></h3>
                                <p class="text-sm text-gray-600 flex items-center gap-2">
                                    <i class="far fa-calendar text-primary"></i>
                                    <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                        <!-- Customer Name Badge -->
                        <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-200 px-4 py-2 rounded-full">
                            <i class="fas fa-user text-blue-600"></i>
                            <span class="text-sm font-semibold text-blue-700">
                                Customer: <?php echo htmlspecialchars($order['customer_name']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="text-left md:text-right">
                        <span class="inline-block px-5 py-2 rounded-full text-sm font-bold border-2 <?php echo $status_color; ?> mb-2">
                            <i class="fas fa-circle text-xs mr-1"></i>
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                        <p class="text-3xl font-bold text-primary">
                            <?php echo format_price($order['total_amount']); ?>
                        </p>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="px-6 py-4">
                    <h4 class="font-semibold text-gray-800 mb-3">Order Items:</h4>
                    <div class="space-y-3">
                        <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                        <div class="flex items-center">
                            <?php 
                            $image_src = $item['image'];
                            if (!empty($image_src) && !filter_var($image_src, FILTER_VALIDATE_URL)) {
                                $image_src = '../assets/images/food/' . $image_src;
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($image_src); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                 class="w-16 h-16 object-cover rounded-lg"
                                 onerror="this.src='https://via.placeholder.com/64?text=Food'">
                            <div class="ml-4 flex-1">
                                <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['name']); ?></p>
                                <p class="text-sm text-gray-600">
                                    Qty: <?php echo $item['quantity']; ?> × <?php echo format_price($item['price']); ?>
                                </p>
                            </div>
                            <p class="font-semibold text-gray-800"><?php echo format_price($item['subtotal']); ?></p>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                
                <!-- Order Details -->
                <div class="px-6 py-4 bg-gray-50 border-t">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-1"><i class="fas fa-map-marker-alt mr-2"></i>Delivery Address:</p>
                            <p class="text-gray-800"><?php echo htmlspecialchars($order['delivery_address']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1"><i class="fas fa-phone mr-2"></i>Contact:</p>
                            <p class="text-gray-800"><?php echo htmlspecialchars($order['phone']); ?></p>
                        </div>
                        <?php if (!empty($order['notes'])): ?>
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600 mb-1"><i class="fas fa-sticky-note mr-2"></i>Notes:</p>
                            <p class="text-gray-800"><?php echo htmlspecialchars($order['notes']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">No orders yet</h2>
            <p class="text-gray-600 mb-6">Start ordering delicious food!</p>
            <a href="menu.php" class="inline-block bg-primary text-white px-8 py-3 rounded-full hover:bg-secondary transition">
                Browse Menu
            </a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
