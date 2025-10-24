<?php
session_start();
require_once '../db.php';

if (!is_logged_in() || !is_admin()) {
    redirect('../index.php');
}

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = sanitize_input($_POST['status']);
    
    // Get current status
    $current_query = "SELECT status FROM orders WHERE id = $order_id";
    $current_result = mysqli_query($conn, $current_query);
    $current_order = mysqli_fetch_assoc($current_result);
    $current_status = $current_order['status'];
    
    // Validate status workflow
    $allowed_transitions = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['preparing', 'cancelled'],
        'preparing' => ['delivered', 'cancelled'],
        'delivered' => [], // Final state - no transitions
        'cancelled' => []  // Final state - no transitions
    ];
    
    // Check if transition is allowed
    $is_valid = false;
    if ($current_status === $new_status) {
        $is_valid = true; // Same status is allowed (no change)
    } elseif (isset($allowed_transitions[$current_status]) && in_array($new_status, $allowed_transitions[$current_status])) {
        $is_valid = true;
    }
    
    if ($is_valid) {
        $update_query = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
        
        if (mysqli_query($conn, $update_query)) {
            // Get order details for notification
            $order_query = "SELECT o.user_id, o.order_number FROM orders o WHERE o.id = {$order_id}";
            $order_result = mysqli_query($conn, $order_query);
            
            if ($order_result && mysqli_num_rows($order_result) > 0) {
                $order = mysqli_fetch_assoc($order_result);
                
                // Create customer notification
                $status_display = ucfirst($new_status);
                $notif_title = "Order {$order['order_number']} - {$status_display}";
                $notif_message = "Your order status has been updated to: {$status_display}";
                $notif_query = "INSERT INTO notifications (user_role, user_id, title, message, related_id) 
                               VALUES ('user', {$order['user_id']}, '{$notif_title}', '{$notif_message}', {$order_id})";
                mysqli_query($conn, $notif_query);
            }
        }
    } else {
        // Invalid transition - could add error message here
        $error = "Invalid status transition from $current_status to $new_status";
    }
}

// Fetch all orders
$orders_query = "SELECT o.*, u.full_name, u.email 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC";
$orders_result = mysqli_query($conn, $orders_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - FoodHub Admin</title>
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

    <nav class="bg-white shadow-xl border-b border-gray-200">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Professional Admin Logo -->
                <div class="flex items-center space-x-3">
                    <div class="relative hidden sm:block">
                        <div class="absolute inset-0 bg-orange-500 rounded-full blur-sm opacity-50"></div>
                        <div class="relative bg-white rounded-full p-2 shadow-md border-2 border-orange-500">
                            <i class="fas fa-utensils text-xl text-orange-500"></i>
                        </div>
                    </div>
                    <span class="text-lg sm:text-xl font-bold text-gray-800">
                        Gourmet Sentinel
                    </span>
                    <span class="text-xs sm:text-sm text-gray-500 hidden sm:inline">| Admin</span>
                </div>
                <a href="../logout.php" class="bg-red-600 px-3 sm:px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-semibold shadow-md text-white text-sm">
                    <i class="fas fa-sign-out-alt mr-1 sm:mr-2"></i><span class="hidden sm:inline">Logout</span><span class="sm:hidden">Exit</span>
                </a>
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
                    
                    <a href="manage_orders.php" class="group flex items-center px-4 py-3.5 mb-1 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg transform hover:scale-105 transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-white bg-opacity-20 mr-3">
                            <i class="fas fa-shopping-bag text-lg"></i>
                        </div>
                        <span class="font-semibold">Orders</span>
                        <i class="fas fa-chevron-right ml-auto text-sm opacity-75"></i>
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

        <main class="flex-1 p-4 sm:p-6 lg:p-8 w-full ml-0 lg:ml-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6 sm:mb-8">Manage Orders</h1>

            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-max">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Order #</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Customer</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase hidden sm:table-cell">Amount</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Status</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase hidden md:table-cell">Date</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm font-medium text-gray-800">
                                    <?php echo htmlspecialchars($order['order_number']); ?>
                                </td>
                                <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-700">
                                    <div><?php echo htmlspecialchars($order['full_name']); ?></div>
                                    <div class="text-xs text-gray-500 hidden sm:block"><?php echo htmlspecialchars($order['email']); ?></div>
                                </td>
                                <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm font-semibold text-gray-800 hidden sm:table-cell">
                                    <?php echo format_price($order['total_amount']); ?>
                                </td>
                                <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm">
                                    <form method="POST" class="inline" onsubmit="return confirmStatusChange(event, '<?php echo $order['status']; ?>')">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status" 
                                                data-current-status="<?php echo $order['status']; ?>"
                                                class="status-select px-2 sm:px-3 py-1 rounded-full text-xs font-semibold border-0 focus:ring-2 focus:ring-primary w-full sm:w-auto
                                                       <?php 
                                                       $status_classes = [
                                                           'pending' => 'bg-yellow-100 text-yellow-800',
                                                           'confirmed' => 'bg-blue-100 text-blue-800',
                                                           'preparing' => 'bg-purple-100 text-purple-800',
                                                           'delivered' => 'bg-green-100 text-green-800',
                                                           'cancelled' => 'bg-red-100 text-red-800'
                                                       ];
                                                       echo $status_classes[$order['status']] ?? '';
                                                       ?>">
                                            <option value="pending" <?php echo $order['status']=='pending'?'selected':''; ?>>Pending</option>
                                            <option value="confirmed" <?php echo $order['status']=='confirmed'?'selected':''; ?>>Confirmed</option>
                                            <option value="preparing" <?php echo $order['status']=='preparing'?'selected':''; ?>>Preparing</option>
                                            <option value="delivered" <?php echo $order['status']=='delivered'?'selected':''; ?>>Delivered</option>
                                            <option value="cancelled" <?php echo $order['status']=='cancelled'?'selected':''; ?>>Cancelled</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-700 hidden md:table-cell">
                                    <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?>
                                </td>
                                <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm">
                                    <button onclick='viewOrder(<?php echo json_encode($order); ?>)' 
                                            class="text-primary hover:text-secondary">
                                        <i class="fas fa-eye mr-1"></i><span class="hidden sm:inline">View</span>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="orderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg p-4 sm:p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4 sm:mb-6">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Order Details</h2>
                <button onclick="document.getElementById('orderModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl sm:text-2xl"></i>
                </button>
            </div>
            <div id="orderDetails" class="text-sm sm:text-base"></div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const closeSidebar = document.getElementById('close-sidebar');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        mobileMenuBtn.addEventListener('click', function() {
            sidebar.classList.add('active');
            overlay.classList.remove('hidden');
        });

        closeSidebar.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.add('hidden');
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.add('hidden');
        });

        // Status workflow validation
        const statusWorkflow = {
            'pending': ['confirmed', 'cancelled'],
            'confirmed': ['preparing', 'cancelled'],
            'preparing': ['delivered', 'cancelled'],
            'delivered': [],
            'cancelled': []
        };

        // Toast helper
        function showToast(message, type = 'info') {
            // Create a simple toast element
            const toast = document.createElement('div');
            toast.className = 'fixed right-4 bottom-6 z-50 px-4 py-2 rounded shadow-md text-white';
            toast.style.minWidth = '200px';
            if (type === 'success') toast.style.backgroundColor = '#16a34a';
            else if (type === 'error') toast.style.backgroundColor = '#dc2626';
            else toast.style.backgroundColor = '#334155';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.transition = 'opacity 0.2s ease';
                toast.style.opacity = '0';
            }, 2500);
            setTimeout(() => toast.remove(), 3000);
        }

        // Initialize all status selects on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.status-select').forEach(function(select) {
                updateStatusOptions(select);

                // Add change event listener for AJAX update
                select.addEventListener('change', function() {
                    const currentStatus = this.dataset.currentStatus;
                    const newStatus = this.value;
                    const orderId = this.closest('form').querySelector('input[name="order_id"]').value;

                    if (currentStatus === newStatus) return;

                    // Validate the workflow sequence
                    const allowed = statusWorkflow[currentStatus] || [];
                    
                    // Check if the transition is valid
                    if (!allowed.includes(newStatus)) {
                        // Show error message with guidance
                        let errorMsg = '';
                        if (currentStatus === 'pending') {
                            errorMsg = 'Please click "Confirmed" first before changing to other statuses.';
                        } else if (currentStatus === 'confirmed') {
                            errorMsg = 'Please click "Preparing" first before marking as Delivered.';
                        } else if (currentStatus === 'preparing') {
                            errorMsg = 'You can only mark as "Delivered" or "Cancelled" from Preparing.';
                        } else if (currentStatus === 'delivered' || currentStatus === 'cancelled') {
                            errorMsg = 'This order has reached a final status and cannot be changed.';
                        }
                        
                        showToast(errorMsg, 'error');
                        this.value = currentStatus; // Reset to current status
                        return;
                    }

                    if (!confirm(`Change order status from ${currentStatus} to ${newStatus}?`)) {
                        this.value = currentStatus;
                        return;
                    }

                    // Disable select while updating
                    this.disabled = true;

                    fetch('update_order_status.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ order_id: orderId, new_status: newStatus })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Update the dataset and visual class for the select
                            select.dataset.currentStatus = newStatus;
                            applyStatusClass(select, newStatus);

                            // Update all selects in the same row if present
                            const row = select.closest('tr');
                            const rowSelects = row.querySelectorAll('.status-select');
                            rowSelects.forEach(rs => {
                                rs.dataset.currentStatus = newStatus;
                                updateStatusOptions(rs);
                                applyStatusClass(rs, newStatus);
                            });

                            showToast('Status updated successfully!', 'success');
                        } else {
                            showToast(data.message || 'Update failed', 'error');
                            select.value = currentStatus;
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showToast('Network error', 'error');
                        select.value = currentStatus;
                    })
                    .finally(() => {
                        select.disabled = false;
                    });
                });
            });
        });

        function updateStatusOptions(select) {
            const currentStatus = select.dataset.currentStatus;
            const allowedStatuses = statusWorkflow[currentStatus] || [];
            
            // ENABLE ALL OPTIONS - only validate on change
            Array.from(select.options).forEach(function(option) {
                const optionValue = option.value;
                
                // Current status is selected
                if (optionValue === currentStatus) {
                    option.selected = true;
                }
                
                // Enable all options
                option.disabled = false;
                option.style.color = '';
                option.style.cursor = '';
            });
        }

        function applyStatusClass(select, status) {
            const classMap = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'confirmed': 'bg-blue-100 text-blue-800',
                'preparing': 'bg-purple-100 text-purple-800',
                'delivered': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800'
            };
            // Remove existing status classes
            Object.values(classMap).forEach(cls => select.classList.remove(...cls.split(' ')));
            // Add current
            const cls = classMap[status];
            if (cls) select.classList.add(...cls.split(' '));
        }

        function confirmStatusChange(event, currentStatus) {
            const form = event.target;
            const newStatus = form.status.value;
            
            if (currentStatus !== newStatus) {
                return confirm(`Confirm status change from ${currentStatus} to ${newStatus}?`);
            }
            return true;
        }

        function viewOrder(order) {
            const details = `
                <div class="space-y-4">
                    <div><strong>Order Number:</strong> ${order.order_number}</div>
                    <div><strong>Customer:</strong> ${order.full_name}</div>
                    <div><strong>Email:</strong> ${order.email}</div>
                    <div><strong>Phone:</strong> ${order.phone}</div>
                    <div><strong>Address:</strong> ${order.delivery_address}</div>
                    <div><strong>Amount:</strong> ₱${parseFloat(order.total_amount).toFixed(2)}</div>
                    <div><strong>Payment:</strong> ${order.payment_mode}</div>
                    ${order.notes ? `<div><strong>Notes:</strong> ${order.notes}</div>` : ''}
                    <div><strong>Status:</strong> <span class="capitalize">${order.status}</span></div>
                    <div><strong>Date:</strong> ${new Date(order.created_at).toLocaleString()}</div>
                </div>
            `;
            document.getElementById('orderDetails').innerHTML = details;
            document.getElementById('orderModal').classList.remove('hidden');
        }
    </script>
</body>
</html>
