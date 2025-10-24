<?php
session_start();
require_once '../db.php';

if (!is_logged_in() || !is_admin()) {
    redirect('../index.php');
}

// Daily sales
$daily_query = "SELECT DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue 
                FROM orders WHERE status != 'cancelled' 
                GROUP BY DATE(created_at) 
                ORDER BY date DESC LIMIT 7";
$daily_result = mysqli_query($conn, $daily_query);

// Monthly sales
$monthly_query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as orders, SUM(total_amount) as revenue 
                  FROM orders WHERE status != 'cancelled' 
                  GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
                  ORDER BY month DESC LIMIT 12";
$monthly_result = mysqli_query($conn, $monthly_query);

// Top selling items
$top_items_query = "SELECT f.name, SUM(oi.quantity) as total_sold, SUM(oi.subtotal) as revenue 
                    FROM order_items oi 
                    JOIN food_items f ON oi.food_item_id = f.id 
                    GROUP BY f.id 
                    ORDER BY total_sold DESC LIMIT 10";
$top_items_result = mysqli_query($conn, $top_items_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - FoodHub Admin</title>
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
    <nav class="bg-white shadow-xl border-b border-gray-200">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Professional Admin Logo -->
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="absolute inset-0 bg-orange-500 rounded-full blur-sm opacity-50"></div>
                        <div class="relative bg-white rounded-full p-2 shadow-md border-2 border-orange-500">
                            <i class="fas fa-utensils text-xl text-orange-500"></i>
                        </div>
                    </div>
                    <span class="text-xl font-bold text-gray-800">
                        Gourmet Sentinel
                    </span>
                    <span class="text-sm text-gray-500">| Admin</span>
                </div>
                <a href="../logout.php" class="bg-red-600 px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-semibold shadow-md text-white">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Enhanced Sidebar -->
        <aside class="w-72 bg-white shadow-2xl min-h-screen border-r border-gray-200">
            <!-- Sidebar Header -->
            <div class="px-6 py-8 border-b border-gray-200 bg-gradient-to-br from-orange-50 to-orange-100">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="relative">
                        <div class="absolute inset-0 bg-orange-500 rounded-lg blur opacity-30"></div>
                        <div class="relative bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg p-3 shadow-lg">
                            <i class="fas fa-shield-alt text-white text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-gray-800 font-bold text-lg">Admin Panel</h3>
                        <p class="text-gray-600 text-xs">Control Center</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="py-4 px-3">
                <!-- Main Section -->
                <div class="mb-6">
                    <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Main Menu</p>
                    
                    <a href="dashboard.php" class="group flex items-center px-4 py-3.5 mb-1 rounded-xl text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 mr-3 group-hover:bg-orange-100 transition-colors">
                            <i class="fas fa-tachometer-alt text-lg text-gray-600 group-hover:text-orange-600"></i>
                        </div>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    
                    <a href="notifications.php" class="group flex items-center px-4 py-3.5 mb-1 rounded-xl text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 mr-3 group-hover:bg-orange-100 transition-colors">
                            <i class="fas fa-bell text-lg text-gray-600 group-hover:text-orange-600"></i>
                        </div>
                        <span class="font-medium">Notifications</span>
                    </a>
                </div>

                <!-- Management Section -->
                <div class="mb-6">
                    <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Management</p>
                    
                    <a href="manage_food.php" class="group flex items-center px-4 py-3.5 mb-1 rounded-xl text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 mr-3 group-hover:bg-orange-100 transition-colors">
                            <i class="fas fa-hamburger text-lg text-gray-600 group-hover:text-orange-600"></i>
                        </div>
                        <span class="font-medium">Food Items</span>
                    </a>
                    
                    <a href="manage_orders.php" class="group flex items-center px-4 py-3.5 mb-1 rounded-xl text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 mr-3 group-hover:bg-orange-100 transition-colors">
                            <i class="fas fa-shopping-bag text-lg text-gray-600 group-hover:text-orange-600"></i>
                        </div>
                        <span class="font-medium">Orders</span>
                    </a>
                    
                    <a href="manage_users.php" class="group flex items-center px-4 py-3.5 mb-1 rounded-xl text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 mr-3 group-hover:bg-orange-100 transition-colors">
                            <i class="fas fa-users text-lg text-gray-600 group-hover:text-orange-600"></i>
                        </div>
                        <span class="font-medium">Customers</span>
                    </a>
                </div>

                <!-- Analytics Section -->
                <div class="mb-6">
                    <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Analytics</p>
                    
                    <a href="reports.php" class="group flex items-center px-4 py-3.5 mb-1 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg transform hover:scale-105 transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-white bg-opacity-20 mr-3">
                            <i class="fas fa-chart-line text-lg"></i>
                        </div>
                        <span class="font-semibold">Sales Reports</span>
                        <i class="fas fa-chevron-right ml-auto text-sm opacity-75"></i>
                    </a>
                </div>

                <!-- Quick Actions -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <a href="../index.php" target="_blank" class="group flex items-center px-4 py-3.5 mb-1 rounded-xl text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 mr-3 group-hover:bg-blue-100 transition-colors">
                            <i class="fas fa-external-link-alt text-lg text-gray-600 group-hover:text-blue-600"></i>
                        </div>
                        <span class="font-medium">View Website</span>
                    </a>
                </div>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Sales Reports</h1>
                <div class="flex gap-3">
                    <a href="export_reports.php?format=excel" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2 shadow-md">
                        <i class="fas fa-file-excel"></i>
                        Download Excel
                    </a>
                    <a href="export_reports.php?format=pdf" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2 shadow-md">
                        <i class="fas fa-file-pdf"></i>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Daily Sales -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Daily Sales (Last 7 Days)</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Orders</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php while ($day = mysqli_fetch_assoc($daily_result)): ?>
                                <tr>
                                    <td class="px-4 py-2 text-sm"><?php echo date('M j, Y', strtotime($day['date'])); ?></td>
                                    <td class="px-4 py-2 text-sm"><?php echo $day['orders']; ?></td>
                                    <td class="px-4 py-2 text-sm font-semibold"><?php echo format_price($day['revenue']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Monthly Sales -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Monthly Sales</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Month</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Orders</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php while ($month = mysqli_fetch_assoc($monthly_result)): ?>
                                <tr>
                                    <td class="px-4 py-2 text-sm"><?php echo date('F Y', strtotime($month['month'].'-01')); ?></td>
                                    <td class="px-4 py-2 text-sm"><?php echo $month['orders']; ?></td>
                                    <td class="px-4 py-2 text-sm font-semibold"><?php echo format_price($month['revenue']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Selling Items -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Top Selling Items</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Item Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Quantity Sold</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while ($item = mysqli_fetch_assoc($top_items_result)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <?php echo $item['total_sold']; ?> units
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                                    <?php echo format_price($item['revenue']); ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
