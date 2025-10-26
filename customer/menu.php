<?php
session_start();
require_once '../db.php';

// Get customer notification count
$user_notif_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $notif_query = "SELECT COUNT(*) as count FROM notifications WHERE user_role = 'user' AND user_id = {$uid} AND is_read = 0";
    $notif_result = mysqli_query($conn, $notif_query);
    if ($notif_result) {
        $user_notif_count = mysqli_fetch_assoc($notif_result)['count'];
    }
}

$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$sort = isset($_GET['sort']) ? sanitize_input($_GET['sort']) : 'name';

$query = "SELECT f.id, f.name, f.description, f.price, f.image, f.category_id, f.is_available, c.name as category_name FROM food_items f INNER JOIN categories c ON f.category_id = c.id WHERE 1=1";

if ($category_filter > 0) {
    $query .= " AND f.category_id = " . (int)$category_filter;
}

if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($conn, $search);
    $query .= " AND (f.name LIKE '%{$search_safe}%' OR f.description LIKE '%{$search_safe}%')";
}

// GROUP BY to ensure each product ID appears only once
$query .= " GROUP BY f.id";

switch ($sort) {
    case 'price_low': $query .= " ORDER BY f.price ASC"; break;
    case 'price_high': $query .= " ORDER BY f.price DESC"; break;
    default: $query .= " ORDER BY f.name ASC"; break;
}

$result = mysqli_query($conn, $query);

// Store unique products in array using ID as key to prevent duplicates
$unique_products = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($item = mysqli_fetch_assoc($result)) {
        $unique_products[$item['id']] = $item; // ID as key ensures uniqueness
    }
}

// Get unique categories - store in array to prevent duplicates
$categories_query = "SELECT id, name FROM categories WHERE is_active = 1 GROUP BY id ORDER BY name";
$categories_result = mysqli_query($conn, $categories_query);

// Store unique categories in array using ID as key
$unique_categories = [];
if ($categories_result && mysqli_num_rows($categories_result) > 0) {
    while ($cat = mysqli_fetch_assoc($categories_result)) {
        $unique_categories[$cat['id']] = $cat; // ID as key ensures uniqueness
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Menu - Gourmet Sentinel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/shared-styles.css">
    <script>tailwind.config = {theme: {extend: {colors: {primary: '#f59e0b', secondary: '#fb923c'}}}}</script>
    <style>
        .notif-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }
        .notif-btn {
            position: relative;
            display: inline-block;
        }
        .notif-toast {
            position: fixed;
            top: 80px;
            right: 20px;
            background: white;
            border-left: 4px solid #f59e0b;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 16px 20px;
            border-radius: 8px;
            max-width: 350px;
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-50">
<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <!-- Modern Logo Design -->
            <a href="../index.php" class="flex items-center space-x-3 group">
                <div class="relative">
                    <!-- Logo Icon with Gradient Background -->
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 via-orange-600 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110 relative overflow-hidden">
                        <!-- Shine Effect -->
                        <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/20 to-transparent transform -skew-x-12"></div>
                        <!-- Icon -->
                        <div class="relative">
                            <i class="fas fa-utensils text-white text-xl"></i>
                        </div>
                    </div>
                </div>
                <!-- Modern Text Logo -->
                <div class="flex flex-col">
                    <span class="text-2xl font-black bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent leading-none tracking-tight">
                        GourmetSentinel
                    </span>
                    <span class="text-[10px] text-gray-500 font-medium tracking-widest uppercase">
                        Food Ordering System
                    </span>
                </div>
            </a>
            <div class="hidden md:flex space-x-8">
                <a href="../index.php" class="text-gray-700 hover:text-primary transition">Home</a>
                <a href="menu.php" class="text-primary font-semibold">Menu</a>
                <a href="../about.php" class="text-gray-700 hover:text-primary transition">About</a>
                <a href="../contact.php" class="text-gray-700 hover:text-primary transition">Contact</a>
            </div>
            <div class="flex items-center space-x-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (!is_admin()): ?>
                    <a href="notifications.php" class="notif-btn text-gray-700 hover:text-primary transition">
                        <i class="fas fa-bell text-xl"></i>
                        <?php if ($user_notif_count > 0): ?>
                        <span class="notif-badge" id="user-notif-count"><?php echo $user_notif_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="cart.php" class="relative text-gray-700 hover:text-primary transition"><i class="fas fa-shopping-cart text-xl"></i><span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span></a>
                    <a href="orders.php" class="text-gray-700 hover:text-primary transition"><i class="fas fa-receipt"></i></a>
                    <?php endif; ?>
                    <div class="relative">
                        <button id="userMenuBtn" class="flex items-center space-x-2 text-gray-700 hover:text-primary transition">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span class="hidden md:inline"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>
                        <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-2 hidden z-[60] border border-gray-100">
                            <?php if (is_admin()): ?>
                                <a href="../admin/dashboard.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                            <?php else: ?>
                                <a href="notifications.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-bell mr-2"></i>Notifications
                                </a>
                                <a href="cart.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-shopping-cart mr-2"></i>My Cart
                                </a>
                                <a href="orders.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-receipt mr-2"></i>My Orders
                                </a>
                            <?php endif; ?>
                            <hr class="my-2">
                            <a href="../logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-50 transition-colors font-medium">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="../auth/login.php" class="text-gray-700 hover:text-primary transition">Login</a>
                    <a href="../auth/register.php" class="bg-gradient-to-r from-primary to-secondary text-white px-6 py-2 rounded-full hover:shadow-lg transition">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-orange-500 to-amber-600 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <!-- Title -->
            <h1 class="text-5xl md:text-6xl font-bold mb-4 drop-shadow-2xl">
                Our Menu
            </h1>
            <p class="text-xl md:text-2xl mb-10 text-white/95 font-light">
                Discover delicious food made with love and fresh ingredients
            </p>
            
            <!-- Enhanced Search Bar -->
            <div class="w-full max-w-2xl mx-auto">
                <form method="GET" action="">
                    <div class="flex items-center bg-white/95 backdrop-blur-md rounded-full shadow-2xl overflow-hidden border border-white/20">
                        <div class="pl-5 pr-2">
                            <i class="fas fa-search text-xl text-gray-500"></i>
                        </div>
                        <input 
                            type="text" 
                            name="search" 
                            value="<?php echo htmlspecialchars($search); ?>" 
                            placeholder="Search for your favorite dishes..." 
                            class="flex-1 px-4 py-4 text-gray-800 focus:outline-none bg-transparent placeholder-gray-500"
                        >
                        <button 
                            type="submit" 
                            class="bg-gradient-to-r from-primary to-secondary text-white px-8 py-3.5 m-1 rounded-full font-semibold hover:shadow-xl transition-all duration-300 flex items-center gap-2 hover:scale-105"
                        >
                            <i class="fas fa-search"></i>
                            <span class="hidden sm:inline">Search</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-6">
            <aside class="lg:w-1/4">
                <div class="bg-white rounded-2xl shadow-lg p-5 sticky top-24 border border-gray-200">
                    <!-- Compact Filter Header -->
                    <div class="mb-5 pb-4 border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-filter text-primary"></i>
                            <span>Filters</span>
                        </h3>
                    </div>
                    
                    <!-- Compact Categories Section -->
                    <div class="mb-5">
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Categories</h4>
                        <div class="space-y-1.5">
                            <a href="menu.php" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-lg transition-all duration-200 <?php echo $category_filter == 0 ? 'bg-gradient-to-r from-primary to-secondary text-white shadow-md' : 'hover:bg-orange-50 text-gray-700 hover:text-primary'; ?>">
                                <i class="fas fa-border-all text-sm <?php echo $category_filter == 0 ? 'text-white' : 'text-gray-500'; ?>"></i>
                                <span class="font-medium text-sm flex-1">All Items</span>
                                <?php if ($category_filter == 0): ?>
                                <i class="fas fa-check text-xs text-white"></i>
                                <?php endif; ?>
                            </a>
                            
                            <?php 
                            $category_icons = [
                                'Burgers' => 'fa-hamburger',
                                'Pizza' => 'fa-pizza-slice',
                                'Salads' => 'fa-salad',
                                'Desserts' => 'fa-ice-cream',
                                'Beverages' => 'fa-glass-martini-alt',
                                'Drinks' => 'fa-glass-water',
                                'Pasta' => 'fa-bowl-food',
                                'Breakfast' => 'fa-bacon',
                                'Seafood' => 'fa-fish'
                            ];
                            
                            foreach ($unique_categories as $category) { 
                                $icon = $category_icons[$category['name']] ?? 'fa-utensils';
                            ?>
                                <a href="menu.php?category=<?php echo $category['id']; ?>" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-lg transition-all duration-200 <?php echo $category_filter == $category['id'] ? 'bg-gradient-to-r from-primary to-secondary text-white shadow-md' : 'hover:bg-orange-50 text-gray-700 hover:text-primary'; ?>">
                                    <i class="fas <?php echo $icon; ?> text-sm <?php echo $category_filter == $category['id'] ? 'text-white' : 'text-gray-500'; ?>"></i>
                                    <span class="font-medium text-sm flex-1"><?php echo htmlspecialchars($category['name']); ?></span>
                                    <?php if ($category_filter == $category['id']): ?>
                                    <i class="fas fa-check text-xs text-white"></i>
                                    <?php endif; ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-4"></div>
                    
                    <!-- Compact Sort Section -->
                    <div>
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Sort By</h4>
                        <form method="GET" action="">
                            <?php if ($category_filter > 0): ?><input type="hidden" name="category" value="<?php echo $category_filter; ?>"><?php endif; ?>
                            <?php if (!empty($search)): ?><input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>"><?php endif; ?>
                            <div class="relative">
                                <select name="sort" onchange="this.form.submit()" class="w-full px-3 py-2.5 pl-9 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary bg-white text-gray-700 text-sm font-medium cursor-pointer hover:border-primary transition-all appearance-none">
                                    <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Name (A-Z)</option>
                                    <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                    <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                </select>
                                <i class="fas fa-sort absolute left-3 top-1/2 -translate-y-1/2 text-primary pointer-events-none text-sm"></i>
                                <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                            </div>
                        </form>
                    </div>
                </div>
            </aside>
            <main class="lg:w-3/4">
                <!-- Results Header -->
                <div class="mb-8 flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h2 class="text-3xl font-black text-gray-800 mb-2">
                            <?php 
                            if ($category_filter > 0) {
                                $cat_name = $unique_categories[$category_filter]['name'] ?? 'Category';
                                echo htmlspecialchars($cat_name);
                            } elseif (!empty($search)) {
                                echo 'Search Results';
                            } else {
                                echo 'All Dishes';
                            }
                            ?>
                        </h2>
                        <p class="text-gray-600 flex items-center gap-2">
                            <i class="fas fa-utensils text-primary"></i>
                            <span class="font-semibold"><?php echo count($unique_products); ?></span> delicious items found
                        </p>
                    </div>
                    <?php if ($category_filter > 0 || !empty($search)): ?>
                    <a href="menu.php" class="flex items-center gap-2 text-primary hover:text-secondary font-semibold transition-colors">
                        <i class="fas fa-times-circle"></i> Clear Filters
                    </a>
                    <?php endif; ?>
                </div>
                
                <!-- Product Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php if (!empty($unique_products)) { foreach ($unique_products as $item) { $image_path = !empty($item['image']) && file_exists('../assets/images/food/' . $item['image']) ? '../assets/images/food/' . $item['image'] : '../assets/images/food/default.jpg'; ?>
                        <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-gray-100 flex flex-col">
                            <!-- Image Container with Overlay -->
                            <div class="relative overflow-hidden" style="height: 260px;">
                                <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-125 group-hover:rotate-3">
                                
                                <!-- Gradient Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                
                                <!-- Category Badge -->
                                <div class="absolute top-4 right-4 z-10">
                                    <span class="bg-white/95 backdrop-blur-sm px-4 py-2 rounded-full text-xs font-bold text-primary shadow-lg border border-orange-100 flex items-center gap-2">
                                        <i class="fas fa-tag"></i>
                                        <?php echo htmlspecialchars($item['category_name']); ?>
                                    </span>
                                </div>
                                
                                <!-- Availability Badge -->
                                <?php if (!$item['is_available']): ?>
                                <div class="absolute top-4 left-4 z-10">
                                    <span class="bg-red-500 text-white px-4 py-2 rounded-full text-xs font-bold shadow-lg flex items-center gap-2">
                                        <i class="fas fa-ban"></i> Out of Stock
                                    </span>
                                </div>
                                <?php else: ?>
                                <div class="absolute top-4 left-4 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                                    <span class="bg-green-500 text-white px-4 py-2 rounded-full text-xs font-bold shadow-lg flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i> Available Now
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Card Content - Fixed Layout -->
                            <div class="p-6 flex flex-col flex-grow">
                                <!-- Title with Fixed Height -->
                                <h3 class="text-xl font-black text-gray-800 mb-3 group-hover:text-primary transition-colors duration-300 line-clamp-1 min-h-[28px]">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </h3>
                                
                                <!-- Description with Fixed Height -->
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2 leading-relaxed min-h-[40px] flex-grow">
                                    <?php echo htmlspecialchars($item['description']); ?>
                                </p>
                                
                                <!-- Divider -->
                                <div class="border-t border-gray-200 my-4"></div>
                                
                                <!-- Price and Action - Always Aligned -->
                                <div class="flex justify-between items-end gap-3 mt-auto">
                                    <!-- Price Section -->
                                    <div class="flex flex-col justify-end">
                                        <span class="text-xs text-gray-500 font-medium mb-1">Price</span>
                                        <span class="text-2xl font-black bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent leading-none">
                                            <?php echo format_price($item['price']); ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Action Button -->
                                    <div class="flex-shrink-0">
                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <?php if (is_admin()): ?>
                                                <div class="bg-gray-100 text-gray-500 px-4 py-2.5 rounded-xl font-semibold cursor-not-allowed text-sm flex items-center gap-2 whitespace-nowrap">
                                                    <i class="fas fa-eye"></i>
                                                    <span>View Only</span>
                                                </div>
                                            <?php else: ?>
                                                <?php if ($item['is_available']): ?>
                                                    <button onclick="addToCart(<?php echo $item['id']; ?>)" class="bg-gradient-to-r from-primary to-secondary text-white px-5 py-2.5 rounded-xl font-bold hover:shadow-xl transition-all duration-300 transform hover:scale-105 active:scale-95 flex items-center gap-2 group/btn whitespace-nowrap">
                                                        <i class="fas fa-cart-plus text-sm group-hover/btn:animate-bounce"></i>
                                                        <span>Add</span>
                                                    </button>
                                                <?php else: ?>
                                                    <button disabled class="bg-gray-200 text-gray-400 px-4 py-2.5 rounded-xl font-semibold cursor-not-allowed text-sm flex items-center gap-2 whitespace-nowrap">
                                                        <i class="fas fa-ban"></i>
                                                        <span>Unavailable</span>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($item['is_available']): ?>
                                                <a href="../auth/login.php?redirect=menu" class="bg-gradient-to-r from-primary to-secondary text-white px-5 py-2.5 rounded-xl font-bold hover:shadow-xl transition-all duration-300 transform hover:scale-105 active:scale-95 flex items-center gap-2 group/btn inline-flex whitespace-nowrap">
                                                    <i class="fas fa-shopping-bag text-sm group-hover/btn:animate-bounce"></i>
                                                    <span>Buy</span>
                                                </a>
                                            <?php else: ?>
                                                <button disabled class="bg-gray-200 text-gray-400 px-4 py-2.5 rounded-xl font-semibold cursor-not-allowed text-sm flex items-center gap-2 whitespace-nowrap">
                                                    <i class="fas fa-ban"></i>
                                                    <span>Unavailable</span>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }} else { ?>
                        <div class="col-span-full text-center py-20">
                            <div class="max-w-md mx-auto">
                                <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-search text-6xl text-gray-300"></i>
                                </div>
                                <h3 class="text-3xl font-bold text-gray-800 mb-3">No items found</h3>
                                <p class="text-gray-500 mb-8 text-lg">We couldn't find any dishes matching your criteria</p>
                                <a href="menu.php" class="inline-flex items-center gap-3 bg-gradient-to-r from-primary to-secondary text-white px-8 py-4 rounded-full font-bold hover:shadow-2xl transition-all transform hover:scale-105">
                                    <i class="fas fa-redo"></i>
                                    <span>Reset Filters</span>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </main>
        </div>
    </div>
</section>
<footer class="bg-gray-100 text-gray-800 py-12 border-t border-gray-200">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div><h3 class="text-2xl font-bold mb-4 text-gray-800"><i class="fas fa-utensils text-primary"></i> Gourmet Sentinel</h3><p class="text-gray-600">Delicious food delivered to your doorstep</p></div>
            <div><h4 class="font-bold mb-4 text-gray-800">Quick Links</h4><ul class="space-y-2"><li><a href="../index.php" class="text-gray-600 hover:text-primary transition-colors">Home</a></li><li><a href="menu.php" class="text-gray-600 hover:text-primary transition-colors">Menu</a></li></ul></div>
            <div><h4 class="font-bold mb-4 text-gray-800">Contact</h4><ul class="space-y-2 text-gray-600"><li><i class="fas fa-phone mr-2 text-primary"></i> +63 123 456 7890</li><li><i class="fas fa-envelope mr-2 text-primary"></i> info@gourmetsentinel.com</li></ul></div>
            <div><h4 class="font-bold mb-4 text-gray-800">Follow Us</h4><div class="flex space-x-4"><a href="#" class="w-10 h-10 bg-primary rounded-full flex items-center justify-center hover:bg-secondary transition-all text-white"><i class="fab fa-facebook-f"></i></a></div></div>
        </div>
        <div class="border-t border-gray-300 mt-8 pt-8 text-center text-gray-600"><p>&copy; 2024 Gourmet Sentinel. All rights reserved.</p></div>
    </div>
</footer>

<!-- Theme Toggle Button -->
<button id="theme-toggle" title="Toggle Dark Mode">
    <i class="fas fa-moon" id="theme-icon"></i>
</button>

<!-- Scroll to Top Button -->
<button id="scrollToTop" class="fixed bottom-24 right-8 bg-primary text-white w-12 h-12 rounded-full shadow-lg hover:bg-secondary transition-all duration-300 opacity-0 invisible z-50 flex items-center justify-center">
    <i class="fas fa-arrow-up text-xl"></i>
</button>

<script>
// Dark Mode Toggle
const themeToggle = document.getElementById('theme-toggle');
const themeIcon = document.getElementById('theme-icon');
const htmlElement = document.documentElement;

// Check for saved theme preference or default to 'light'
const currentTheme = localStorage.getItem('theme') || 'light';
htmlElement.setAttribute('data-theme', currentTheme);
updateThemeIcon(currentTheme);

themeToggle.addEventListener('click', function() {
    const currentTheme = htmlElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    htmlElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon(newTheme);
});

function updateThemeIcon(theme) {
    if (theme === 'dark') {
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
    } else {
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
    }
}

// Scroll to Top Button
const scrollToTopBtn = document.getElementById('scrollToTop');

window.addEventListener('scroll', function() {
    if (window.pageYOffset > 300) {
        scrollToTopBtn.classList.remove('opacity-0', 'invisible');
        scrollToTopBtn.classList.add('opacity-100', 'visible');
    } else {
        scrollToTopBtn.classList.add('opacity-0', 'invisible');
        scrollToTopBtn.classList.remove('opacity-100', 'visible');
    }
});

scrollToTopBtn.addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// Cart functions
function updateCartCount() { fetch('get_cart_count.php').then(r => r.json()).then(d => { const cartCountElem = document.getElementById('cart-count'); if(cartCountElem) cartCountElem.textContent = d.count; }); }
function addToCart(foodId) { fetch('cart_handler.php', {method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'action=add&food_id=' + foodId + '&quantity=1'}).then(r => r.json()).then(d => { if (d.success) { updateCartCount(); const m = document.createElement('div'); m.className = 'fixed top-24 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50'; m.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Added!'; document.body.appendChild(m); setTimeout(() => m.remove(), 3000); } else { alert(d.message || 'Error'); } }); }
<?php if (isset($_SESSION['user_id']) && !is_admin()): ?>updateCartCount();<?php endif; ?>

// Customer notification polling
<?php if (isset($_SESSION['user_id'])): ?>
(function pollUserNotifications() {
    let lastNotificationId = 0;
    
    function updateBadge(count) {
        const badge = document.getElementById('user-notif-count');
        const bellLink = document.querySelector('.notif-btn');
        
        if (count > 0) {
            if (badge) {
                badge.textContent = count;
            } else if (bellLink) {
                const newBadge = document.createElement('span');
                newBadge.className = 'notif-badge';
                newBadge.id = 'user-notif-count';
                newBadge.textContent = count;
                bellLink.appendChild(newBadge);
            }
        } else if (badge) {
            badge.remove();
        }
    }
    
    function showToast(notification) {
        const toast = document.createElement('div');
        toast.className = 'notif-toast';
        toast.innerHTML = `
            <div style="font-weight: bold; margin-bottom: 4px; color: #f59e0b;">
                <i class="fas fa-bell mr-2"></i>${notification.title}
            </div>
            <div style="color: #666; font-size: 14px;">
                ${notification.message}
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => toast.remove(), 300);
        }, 7000);
    }
    
    async function fetchNotifications() {
        try {
            const response = await fetch('notif_count.php');
            const data = await response.json();
            
            updateBadge(data.count || 0);
            
            if (data.latest && data.latest.id && data.latest.id !== lastNotificationId) {
                showToast(data.latest);
                lastNotificationId = data.latest.id;
            }
        } catch (error) {
            console.error('Error fetching notifications:', error);
        }
    }
    
    // Initial fetch
    fetchNotifications();
    
    // Poll every 8 seconds
    setInterval(fetchNotifications, 8000);
})();
<?php endif; ?>

// User dropdown menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
        
        // Prevent dropdown from closing when clicking inside it
        userDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
</script>
</body>
</html>
