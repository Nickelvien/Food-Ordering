<?php
session_start();
require_once '../db.php';

if (!is_logged_in() || !is_admin()) {
    redirect('../index.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id = $id AND role = 'customer'");
}

// Fetch all customers
$users_query = "SELECT * FROM users WHERE role = 'customer' ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - FoodHub Admin</title>
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
                    
                    <a href="manage_users.php" class="group flex items-center px-4 py-3.5 mb-1 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg transform hover:scale-105 transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-white bg-opacity-20 mr-3">
                            <i class="fas fa-users text-lg"></i>
                        </div>
                        <span class="font-semibold">Customers</span>
                        <i class="fas fa-chevron-right ml-auto text-sm opacity-75"></i>
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

        <main class="flex-1 p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Manage Users</h1>

            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Registered</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-700"><?php echo $user['id']; ?></td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                    <?php echo htmlspecialchars($user['full_name']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="?delete=<?php echo $user['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this user?')"
                                       class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
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
