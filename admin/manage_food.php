<?php
session_start();
require_once '../db.php';

// Redirect if not logged in or not admin
if (!is_logged_in() || !is_admin()) {
    redirect('../index.php');
}

$error = '';
$success = '';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $delete_query = "DELETE FROM food_items WHERE id = $id";
    if (mysqli_query($conn, $delete_query)) {
        $success = 'Food item deleted successfully';
    } else {
        $error = 'Failed to delete food item';
    }
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $category_id = (int)$_POST['category_id'];
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    $price = (float)$_POST['price'];
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Stock quantity validation
    $stock_quantity = isset($_POST['stock_quantity']) ? $_POST['stock_quantity'] : 0;
    if (!is_numeric($stock_quantity) || $stock_quantity < 0) {
        $error = 'Stock quantity must be a non-negative number';
        $stock_quantity = 0;
    } else {
        $stock_quantity = (int)$stock_quantity;
    }

    // Existing image (from hidden input when editing)
    $existing_image = isset($_POST['existing_image']) ? sanitize_input($_POST['existing_image']) : '';
    $image = $existing_image; // default to existing

    // Handle uploaded file if present
    if (isset($_FILES['image_file']) && isset($_FILES['image_file']['error']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image_file'];
        // Basic validations
        $allowed_types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $max_size = 2 * 1024 * 1024; // 2 MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'File upload error';
        } elseif ($file['size'] > $max_size) {
            $error = 'Image is too large. Max 2MB allowed.';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!array_key_exists($mime, $allowed_types)) {
                $error = 'Invalid image type. Only JPG, PNG, WEBP, GIF allowed.';
            } else {
                $ext = $allowed_types[$mime];
                // generate unique filename
                if (function_exists('random_bytes')) {
                    $unique = bin2hex(random_bytes(5));
                } else {
                    $unique = uniqid();
                }
                $new_name = time() . '_' . $unique . '.' . $ext;

                $target_dir = __DIR__ . '/../assets/images/food/';
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }

                $target_path = $target_dir . $new_name;
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    $image = $new_name;
                    // If there was an old local file, delete it
                    if (!empty($existing_image) && !filter_var($existing_image, FILTER_VALIDATE_URL)) {
                        $old_path = $target_dir . $existing_image;
                        if (file_exists($old_path) && $existing_image !== $image) {
                            @unlink($old_path);
                        }
                    }
                } else {
                    $error = 'Failed to move uploaded file.';
                }
            }
        }
    }

    // If there was an error during upload/validation skip DB ops
    if (empty($error)) {
        // Use prepared statements for safety
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "UPDATE food_items SET category_id = ?, name = ?, description = ?, price = ?, image = ?, is_available = ?, is_featured = ?, stock_quantity = ? WHERE id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'issdsiiii', $category_id, $name, $description, $price, $image, $is_available, $is_featured, $stock_quantity, $id);
                if (mysqli_stmt_execute($stmt)) {
                    $success = 'Food item updated successfully';
                } else {
                    $error = 'Failed to update food item';
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = 'Failed to prepare update statement';
            }
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO food_items (category_id, name, description, price, image, is_available, is_featured, stock_quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'issdsiii', $category_id, $name, $description, $price, $image, $is_available, $is_featured, $stock_quantity);
                if (mysqli_stmt_execute($stmt)) {
                    $success = 'Food item added successfully';
                } else {
                    $error = 'Failed to add food item: ' . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = 'Failed to prepare insert statement: ' . mysqli_error($conn);
            }
        }
    }
}

// Get filter parameters
$filter_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$filter_featured = isset($_GET['featured']) ? $_GET['featured'] : 'all';

// Build query with filters
$food_query = "SELECT f.*, c.name as category_name FROM food_items f 
               JOIN categories c ON f.category_id = c.id WHERE 1=1";

// Apply category filter
if ($filter_category > 0) {
    $food_query .= " AND f.category_id = " . $filter_category;
}

// Apply status filter
if ($filter_status === 'available') {
    $food_query .= " AND f.is_available = 1";
} elseif ($filter_status === 'unavailable') {
    $food_query .= " AND f.is_available = 0";
}

// Apply featured filter
if ($filter_featured === 'yes') {
    $food_query .= " AND f.is_featured = 1";
} elseif ($filter_featured === 'no') {
    $food_query .= " AND f.is_featured = 0";
}

$food_query .= " ORDER BY f.created_at DESC";
$food_result = mysqli_query($conn, $food_query);

// Fetch categories for dropdown
$categories_query = "SELECT * FROM categories WHERE is_active = 1";
$categories_result = mysqli_query($conn, $categories_query);

// Fetch categories again for filter dropdown (since we use the result twice)
$filter_categories_query = "SELECT * FROM categories WHERE is_active = 1 ORDER BY name";
$filter_categories_result = mysqli_query($conn, $filter_categories_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Food - FoodHub Admin</title>
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
                        <?php 
                        $notif_count = 0;
                        $notif_query = "SELECT COUNT(*) as count FROM notifications WHERE user_role = 'admin' AND is_read = 0";
                        $notif_result = mysqli_query($conn, $notif_query);
                        if ($notif_result) {
                            $notif_count = mysqli_fetch_assoc($notif_result)['count'];
                        }
                        if ($notif_count > 0): ?>
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
                    
                    <a href="manage_food.php" class="group flex items-center px-4 py-3.5 mb-1 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg transform hover:scale-105 transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-white bg-opacity-20 mr-3">
                            <i class="fas fa-hamburger text-lg"></i>
                        </div>
                        <span class="font-semibold">Food Items</span>
                        <i class="fas fa-chevron-right ml-auto text-sm opacity-75"></i>
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

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Manage Food Items</h1>
                <button onclick="openModal()" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-secondary transition">
                    <i class="fas fa-plus mr-2"></i>Add New Item
                </button>
            </div>

            <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>

            <!-- Filters Section -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-filter mr-2 text-primary"></i>Filter Products
                    </h2>
                    <a href="manage_food.php" class="text-sm text-gray-600 hover:text-primary transition">
                        <i class="fas fa-redo mr-1"></i>Reset Filters
                    </a>
                </div>
                
                <form method="GET" action="manage_food.php" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tags mr-1"></i>Category
                        </label>
                        <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="0">All Categories</option>
                            <?php while ($cat = mysqli_fetch_assoc($filter_categories_result)): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $filter_category == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Stock Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-box mr-1"></i>Stock Status
                        </label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="all" <?php echo $filter_status === 'all' ? 'selected' : ''; ?>>All Status</option>
                            <option value="available" <?php echo $filter_status === 'available' ? 'selected' : ''; ?>>In Stock</option>
                            <option value="unavailable" <?php echo $filter_status === 'unavailable' ? 'selected' : ''; ?>>Out of Stock</option>
                        </select>
                    </div>

                    <!-- Featured Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-star mr-1"></i>Featured
                        </label>
                        <select name="featured" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="all" <?php echo $filter_featured === 'all' ? 'selected' : ''; ?>>All Products</option>
                            <option value="yes" <?php echo $filter_featured === 'yes' ? 'selected' : ''; ?>>Featured Only</option>
                            <option value="no" <?php echo $filter_featured === 'no' ? 'selected' : ''; ?>>Not Featured</option>
                        </select>
                    </div>

                    <!-- Apply Button -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary text-white px-6 py-2 rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                            <i class="fas fa-search mr-2"></i>Apply Filters
                        </button>
                    </div>
                </form>

                <!-- Active Filters Display -->
                <?php if ($filter_category > 0 || $filter_status !== 'all' || $filter_featured !== 'all'): ?>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm text-gray-600 font-medium">Active Filters:</span>
                        
                        <?php if ($filter_category > 0): 
                            // Get category name
                            mysqli_data_seek($filter_categories_result, 0);
                            while ($cat = mysqli_fetch_assoc($filter_categories_result)) {
                                if ($cat['id'] == $filter_category) {
                                    echo '<span class="inline-flex items-center gap-1 bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-medium">
                                            <i class="fas fa-tags"></i> ' . htmlspecialchars($cat['name']) . '
                                          </span>';
                                    break;
                                }
                            }
                        endif; ?>
                        
                        <?php if ($filter_status === 'available'): ?>
                            <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                                <i class="fas fa-check-circle"></i> In Stock
                            </span>
                        <?php elseif ($filter_status === 'unavailable'): ?>
                            <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">
                                <i class="fas fa-times-circle"></i> Out of Stock
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($filter_featured === 'yes'): ?>
                            <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-medium">
                                <i class="fas fa-star"></i> Featured
                            </span>
                        <?php elseif ($filter_featured === 'no'): ?>
                            <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">
                                <i class="far fa-star"></i> Not Featured
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Food Items Table -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Results Counter -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-utensils mr-2 text-primary"></i>
                            Showing <span class="font-semibold text-gray-800"><?php echo mysqli_num_rows($food_result); ?></span> product(s)
                        </p>
                        <?php if ($filter_category > 0 || $filter_status !== 'all' || $filter_featured !== 'all'): ?>
                        <a href="manage_food.php" class="text-sm text-primary hover:text-secondary transition">
                            <i class="fas fa-times-circle mr-1"></i>Clear all filters
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Image</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Featured</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while ($item = mysqli_fetch_assoc($food_result)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
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
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <?php echo htmlspecialchars($item['category_name']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                                    <?php echo format_price($item['price']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <!-- Stock Quantity Display -->
                                    <div class="flex items-center gap-2">
                                        <?php 
                                        $stock_qty = isset($item['stock_quantity']) ? (int)$item['stock_quantity'] : 0;
                                        $stock_class = '';
                                        $stock_icon = '';
                                        
                                        if ($stock_qty == 0) {
                                            $stock_class = 'bg-red-100 text-red-800 border-red-300';
                                            $stock_icon = 'fa-exclamation-triangle';
                                        } elseif ($stock_qty <= 10) {
                                            $stock_class = 'bg-yellow-100 text-yellow-800 border-yellow-300';
                                            $stock_icon = 'fa-exclamation-circle';
                                        } else {
                                            $stock_class = 'bg-green-100 text-green-800 border-green-300';
                                            $stock_icon = 'fa-box';
                                        }
                                        ?>
                                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold <?php echo $stock_class; ?> border flex items-center gap-2">
                                            <i class="fas <?php echo $stock_icon; ?>"></i>
                                            <?php echo $stock_qty; ?> units
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <!-- Stock Status Toggle -->
                                    <div class="flex items-center gap-2">
                                        <?php if ($item['is_available']): ?>
                                        <button onclick="toggleStock(<?php echo $item['id']; ?>, 0)" 
                                                class="px-4 py-2 rounded-lg text-xs font-semibold bg-green-100 text-green-800 hover:bg-green-200 transition-all duration-300 border border-green-300 flex items-center gap-2">
                                            <i class="fas fa-check-circle"></i>
                                            In Stock
                                        </button>
                                        <?php else: ?>
                                        <button onclick="toggleStock(<?php echo $item['id']; ?>, 1)" 
                                                class="px-4 py-2 rounded-lg text-xs font-semibold bg-red-100 text-red-800 hover:bg-red-200 transition-all duration-300 border border-red-300 flex items-center gap-2">
                                            <i class="fas fa-times-circle"></i>
                                            Out of Stock
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <?php if ($item['is_featured']): ?>
                                    <i class="fas fa-star text-yellow-500"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <button onclick='editItem(<?php echo json_encode($item); ?>)' 
                                            class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $item['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this item?')"
                                       class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
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

    <!-- Add/Edit Modal -->
    <div id="foodModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800" id="modalTitle">Add New Food Item</h2>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <form method="POST" action="manage_food.php" id="foodForm" enctype="multipart/form-data">
                <input type="hidden" name="id" id="food_id">
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="col-span-2">
                        <label class="block text-gray-700 font-medium mb-2">Food Name *</label>
                        <input type="text" name="name" id="food_name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Category *</label>
                        <select name="category_id" id="food_category" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                            <?php mysqli_data_seek($categories_result, 0); ?>
                            <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Price *</label>
                        <input type="number" step="0.01" name="price" id="food_price" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-box mr-1 text-primary"></i>Stock Quantity *
                        </label>
                        <input type="number" name="stock_quantity" id="food_stock" value="0" min="0" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                               placeholder="Enter stock quantity">
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle"></i> Number of units available in inventory
                        </p>
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-gray-700 font-medium mb-2">Description</label>
                        <textarea name="description" id="food_description" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-gray-700 font-medium mb-2">Upload Image *</label>
                        <input type="file" name="image_file" id="food_image_file" accept="image/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        <input type="hidden" name="existing_image" id="existing_image">
                        <div class="mt-3">
                            <img id="image_preview" src="" alt="Preview" class="w-24 h-24 object-cover rounded-lg hidden">
                        </div>
                    </div>
                    
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_available" id="food_available" checked
                                   class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                            <span class="ml-2 text-gray-700">Available</span>
                        </label>
                    </div>
                    
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_featured" id="food_featured"
                                   class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                            <span class="ml-2 text-gray-700">Featured Item</span>
                        </label>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-primary text-white py-3 rounded-lg font-semibold hover:bg-secondary transition">
                        <i class="fas fa-save mr-2"></i>Save Food Item
                    </button>
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-400 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('foodModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Add New Food Item';
            document.getElementById('foodForm').reset();
            document.getElementById('food_id').value = '';
        }

        function closeModal() {
            document.getElementById('foodModal').classList.add('hidden');
        }

        function editItem(item) {
            document.getElementById('foodModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Edit Food Item';
            document.getElementById('food_id').value = item.id;
            document.getElementById('food_name').value = item.name;
            document.getElementById('food_category').value = item.category_id;
            document.getElementById('food_price').value = item.price;
            document.getElementById('food_stock').value = item.stock_quantity || 0;
            document.getElementById('food_description').value = item.description;
            // Set existing image (filename or URL)
            document.getElementById('existing_image').value = item.image;
            var preview = document.getElementById('image_preview');
            if (item.image) {
                var src = item.image;
                if (!src.startsWith('http')) {
                    src = '../assets/images/food/' + src;
                }
                preview.src = src;
                preview.classList.remove('hidden');
            } else {
                preview.src = '';
                preview.classList.add('hidden');
            }
            document.getElementById('food_available').checked = item.is_available == 1;
            document.getElementById('food_featured').checked = item.is_featured == 1;
        }

        // Toggle stock status
        function toggleStock(foodId, newStatus) {
            if (!confirm('Are you sure you want to change the stock status? This will notify all users.')) {
                return;
            }

            fetch('update_stock.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    food_id: foodId,
                    is_available: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert('Stock status updated successfully! Users have been notified.');
                    // Reload page to show updated status
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update stock status');
            });
        }

        // Preview newly selected file
        document.getElementById('food_image_file').addEventListener('change', function (e) {
            var file = e.target.files[0];
            var preview = document.getElementById('image_preview');
            if (file) {
                var reader = new FileReader();
                reader.onload = function (ev) {
                    preview.src = ev.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.classList.add('hidden');
            }
        });

        // Stock quantity validation
        document.getElementById('food_stock').addEventListener('input', function(e) {
            var value = parseInt(e.target.value);
            if (isNaN(value) || value < 0) {
                e.target.value = 0;
                showNotification('Stock quantity must be a non-negative number', 'error');
            }
        });

        // Form validation before submit
        document.getElementById('foodForm').addEventListener('submit', function(e) {
            var stockValue = parseInt(document.getElementById('food_stock').value);
            if (isNaN(stockValue) || stockValue < 0) {
                e.preventDefault();
                showNotification('Please enter a valid stock quantity (0 or greater)', 'error');
                return false;
            }
        });

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const closeSidebar = document.getElementById('close-sidebar');
        const sidebar = document.querySelector('.sidebar');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                sidebar.classList.add('active');
            });
        }

        if (closeSidebar) {
            closeSidebar.addEventListener('click', function() {
                sidebar.classList.remove('active');
            });
        }

        // Close sidebar when clicking on a link
        const sidebarLinks = document.querySelectorAll('.sidebar a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                sidebar.classList.remove('active');
            });
        });

        // Show notification helper
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-20 right-4 px-6 py-4 rounded-lg shadow-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</body>
</html>
