<?php
session_start();
require_once '../db.php';

// Redirect if not logged in or not admin
if (!is_logged_in() || !is_admin()) {
    redirect('../index.php');
}

// Get statistics
$stats = [];

// Total users
$users_query = "SELECT COUNT(*) as count FROM users WHERE role = 'customer'";
$users_result = mysqli_query($conn, $users_query);
$stats['users'] = mysqli_fetch_assoc($users_result)['count'];

// Total orders
$orders_query = "SELECT COUNT(*) as count FROM orders";
$orders_result = mysqli_query($conn, $orders_query);
$stats['orders'] = mysqli_fetch_assoc($orders_result)['count'];

// Total revenue
$revenue_query = "SELECT SUM(total_amount) as revenue FROM orders WHERE status != 'cancelled'";
$revenue_result = mysqli_query($conn, $revenue_query);
$stats['revenue'] = mysqli_fetch_assoc($revenue_result)['revenue'] ?? 0;

// Total food items
$items_query = "SELECT COUNT(*) as count FROM food_items";
$items_result = mysqli_query($conn, $items_query);
$stats['items'] = mysqli_fetch_assoc($items_result)['count'];

// Pending orders
$pending_query = "SELECT COUNT(*) as count FROM orders WHERE status = 'pending'";
$pending_result = mysqli_query($conn, $pending_query);
$stats['pending'] = mysqli_fetch_assoc($pending_result)['count'];

// Recent orders
$recent_orders_query = "SELECT o.*, u.full_name 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       ORDER BY o.created_at DESC 
                       LIMIT 5";
$recent_orders = mysqli_query($conn, $recent_orders_query);

// Get notification count
$notif_count = 0;
$notif_query = "SELECT COUNT(*) as count FROM notifications WHERE user_role = 'admin' AND is_read = 0";
$notif_result = mysqli_query($conn, $notif_query);
if ($notif_result) {
    $notif_count = mysqli_fetch_assoc($notif_result)['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Gourmet Sentinel</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Mobile sidebar toggle */
        @media (max-width: 1023px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            .sidebar.active {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Mobile Menu Button -->
    <button id="mobile-menu-btn" class="lg:hidden fixed top-4 left-4 z-50 bg-orange-500 text-white p-3 rounded-lg shadow-lg hover:bg-orange-600">
        <i class="fas fa-bars text-xl"></i>
    </button>

    <!-- Navigation -->
    <nav class="bg-white shadow-xl border-b border-gray-200">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Modern Admin Logo -->
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 via-orange-600 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-105 relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/20 to-transparent transform -skew-x-12"></div>
                            <div class="relative">
                                <i class="fas fa-utensils text-white text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-black bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent leading-none tracking-tight">
                            GourmetSentinel
                        </span>
                        <span class="text-[10px] text-gray-500 font-bold tracking-widest uppercase">
                            Admin Panel
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <a href="notifications.php" class="notif-btn relative p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-bell text-lg sm:text-xl text-gray-700"></i>
                        <?php if ($notif_count > 0): ?>
                        <span class="notif-badge absolute -top-1 -right-1 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-bold shadow-lg" id="admin-notif-count"><?php echo $notif_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <span class="text-gray-700 hidden md:inline text-sm">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <a href="../logout.php" class="bg-red-600 px-3 sm:px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-semibold shadow-md text-white text-sm">
                        <i class="fas fa-sign-out-alt mr-1 sm:mr-2"></i><span class="hidden sm:inline">Logout</span><span class="sm:hidden">Exit</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex relative">
        <!-- Enhanced Sidebar -->
        <aside class="sidebar w-72 bg-white shadow-2xl min-h-screen border-r border-gray-200 fixed lg:static z-40 lg:z-auto">
            <!-- Close button for mobile -->
            <button id="close-sidebar" class="lg:hidden absolute top-4 right-4 text-gray-600 hover:text-gray-800">
                <i class="fas fa-times text-2xl"></i>
            </button>
            
            <!-- Sidebar Header -->
            <div class="px-6 py-8 border-b border-gray-200 bg-gradient-to-br from-orange-50 to-orange-100 mt-16 lg:mt-0">
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
                    
                    <a href="dashboard.php" class="group flex items-center px-4 py-3.5 mb-1 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg transform hover:scale-105 transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-white bg-opacity-20 mr-3">
                            <i class="fas fa-tachometer-alt text-lg"></i>
                        </div>
                        <span class="font-semibold">Dashboard</span>
                        <i class="fas fa-chevron-right ml-auto text-sm opacity-75"></i>
                    </a>
                    
                    <a href="notifications.php" class="group flex items-center px-4 py-3.5 mb-1 rounded-xl text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 mr-3 group-hover:bg-orange-100 transition-colors">
                            <i class="fas fa-bell text-lg text-gray-600 group-hover:text-orange-600"></i>
                        </div>
                        <span class="font-medium">Notifications</span>
                        <?php if ($notif_count > 0): ?>
                        <span class="ml-auto bg-red-500 text-white text-xs px-2.5 py-1 rounded-full font-bold shadow-lg animate-pulse"><?php echo $notif_count; ?></span>
                        <?php endif; ?>
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
                    
                    <a href="reports.php" class="group flex items-center px-4 py-3.5 mb-1 rounded-xl text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 mr-3 group-hover:bg-orange-100 transition-colors">
                            <i class="fas fa-chart-line text-lg text-gray-600 group-hover:text-orange-600"></i>
                        </div>
                        <span class="font-medium">Sales Reports</span>
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

        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 w-full">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6 sm:mb-8">Dashboard Overview</h1>
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Total Orders</p>
                            <p class="text-3xl font-bold text-gray-800"><?php echo $stats['orders']; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-shopping-bag text-2xl text-blue-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Total Revenue</p>
                            <p class="text-3xl font-bold text-gray-800"><?php echo format_price($stats['revenue']); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl text-green-600 font-bold">₱</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Total Customers</p>
                            <p class="text-3xl font-bold text-gray-800"><?php echo $stats['users']; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-2xl text-purple-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Pending Orders</p>
                            <p class="text-3xl font-bold text-gray-800"><?php echo $stats['pending']; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-2xl text-orange-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-xl font-bold text-gray-800">Recent Orders</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (mysqli_num_rows($recent_orders) > 0): ?>
                                <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                        <?php echo htmlspecialchars($order['order_number']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <?php echo htmlspecialchars($order['full_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                                        <?php echo format_price($order['total_amount']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <?php
                                        $status_colors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'confirmed' => 'bg-blue-100 text-blue-800',
                                            'preparing' => 'bg-purple-100 text-purple-800',
                                            'delivered' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ];
                                        $status_color = $status_colors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $status_color; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="manage_orders.php" class="text-primary hover:text-secondary">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        No orders yet
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sales Analytics Chart -->
            <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Sales Analytics</h2>
                    <span class="text-sm text-gray-600">Last 7 Days</span>
                </div>
                <div class="relative" style="height: 350px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </main>
    </div>

    <!-- Chart.js Initialization -->
    <script>
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const closeSidebar = document.getElementById('close-sidebar');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            sidebar.classList.add('active');
            overlay.classList.remove('hidden');
        });
    }

    if (closeSidebar) {
        closeSidebar.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.add('hidden');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.add('hidden');
        });
    }

    // Load and render sales chart
    async function loadSalesChart() {
        try {
            const response = await fetch('sales_data.php');
            const data = await response.json();
            
            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Sales (₱)',
                        data: data.values,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.35,
                        fill: true,
                        borderWidth: 3,
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1200,
                        easing: 'easeInOutQuart'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                usePointStyle: true,
                                padding: 15
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            callbacks: {
                                label: function(context) {
                                    return 'Sales: ₱' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error loading sales chart:', error);
        }
    }

    // Load chart on page load
    loadSalesChart();

    // Notification badge auto-update
    (function pollAdminNotifications() {
        function updateBadge(count) {
            const badge = document.getElementById('admin-notif-count');
            const bellLink = document.querySelector('.notif-btn');
            
            if (count > 0) {
                if (badge) {
                    badge.textContent = count;
                } else if (bellLink) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'notif-badge';
                    newBadge.id = 'admin-notif-count';
                    newBadge.textContent = count;
                    bellLink.appendChild(newBadge);
                }
            } else if (badge) {
                badge.remove();
            }
        }

        async function fetchNotificationCount() {
            try {
                const response = await fetch('notif_count.php');
                const data = await response.json();
                updateBadge(data.count || 0);
            } catch (error) {
                console.error('Error fetching notifications:', error);
            }
        }

        // Initial fetch
        fetchNotificationCount();
        
        // Poll every 10 seconds
        setInterval(fetchNotificationCount, 10000);
    })();
    </script>
</body>
</html>
