<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gourmet Sentinel - Delicious Food Delivered to Your Door</title>
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
        .hero-gradient {
            background: linear-gradient(135deg, #f59e0b 0%, #fb923c 100%);
        }
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        /* Hero Section Animations */
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        
        @keyframes float-delay {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-15px);
            }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .animate-float-delay {
            animation: float-delay 6s ease-in-out infinite;
            animation-delay: 1s;
        }
        
        /* Text gradient animation */
        @keyframes gradient-shift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }
        
        .bg-gradient-to-r {
            background-size: 200% 200%;
            animation: gradient-shift 3s ease infinite;
        }
        
        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }
        
        /* Enhanced backdrop blur for better readability */
        @supports (backdrop-filter: blur(10px)) {
            .backdrop-blur-sm {
                backdrop-filter: blur(10px);
            }
            .backdrop-blur-md {
                backdrop-filter: blur(15px);
            }
        }
        
        /* Hero section positioning */
        .hero-section {
            margin-top: -20px; /* move the whole hero section slightly upward */
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php
    session_start();
    require_once 'db.php';
    
    // Fetch featured food items
    $featured_query = "SELECT f.*, c.name as category_name 
                      FROM food_items f 
                      JOIN categories c ON f.category_id = c.id 
                      WHERE f.is_featured = 1 AND f.is_available = 1 
                      ORDER BY f.id DESC
                      LIMIT 8";
    $featured_result = mysqli_query($conn, $featured_query);
    
    // Fetch categories
    $categories_query = "SELECT * FROM categories WHERE is_active = 1 LIMIT 4";
    $categories_result = mysqli_query($conn, $categories_query);
    ?>

    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
            <!-- Modern Logo Design -->
            <a href="index.php" class="flex items-center space-x-3 group">
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 via-orange-600 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/20 to-transparent transform -skew-x-12"></div>
                        <div class="relative">
                            <i class="fas fa-utensils text-white text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="text-2xl font-black bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent leading-none tracking-tight">
                        GourmetSentinel
                    </span>
                    <span class="text-[10px] text-gray-500 font-medium tracking-widest uppercase">
                        Food Ordering System
                    </span>
                </div>
            </a>                <div class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-primary font-medium transition-colors">Home</a>
                    <?php if (!is_admin()): ?>
                    <a href="customer/menu.php" class="text-gray-700 hover:text-primary font-medium transition-colors">Menu</a>
                    <?php endif; ?>
                    <a href="about.php" class="text-gray-700 hover:text-primary font-medium transition-colors">About</a>
                    <a href="contact.php" class="text-gray-700 hover:text-primary font-medium transition-colors">Contact</a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <?php if (!is_admin()): ?>
                    <?php if (is_logged_in()): ?>
                    <!-- Notification Icon for logged in users -->
                    <?php
                    $user_notif_count = 0;
                    if (isset($_SESSION['user_id'])) {
                        $uid = (int)$_SESSION['user_id'];
                        $notif_query = "SELECT COUNT(*) as count FROM notifications WHERE user_role = 'user' AND user_id = {$uid} AND is_read = 0";
                        $notif_result = mysqli_query($conn, $notif_query);
                        if ($notif_result) {
                            $user_notif_count = mysqli_fetch_assoc($notif_result)['count'];
                        }
                    }
                    ?>
                    <a href="customer/notifications.php" class="relative text-gray-700 hover:text-primary transition-colors">
                        <i class="fas fa-bell text-xl"></i>
                        <?php if ($user_notif_count > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                            <?php echo $user_notif_count; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                    <a href="customer/cart.php" class="relative text-gray-700 hover:text-primary transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <?php if (get_cart_count() > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                            <?php echo get_cart_count(); ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (is_logged_in()): ?>
                        <div class="relative">
                            <button id="userMenuBtn" class="flex items-center space-x-2 text-gray-700 hover:text-primary transition-colors">
                                <i class="fas fa-user-circle text-xl"></i>
                                <span class="hidden md:inline"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                                <i class="fas fa-chevron-down text-sm"></i>
                            </button>
                            <div id="userMenuDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden z-50">
                                <?php if (is_admin()): ?>
                                    <a href="admin/dashboard.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                    </a>
                                <?php else: ?>
                                    <a href="customer/cart.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-shopping-cart mr-2"></i>My Cart
                                    </a>
                                    <a href="customer/orders.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-receipt mr-2"></i>My Orders
                                    </a>
                                <?php endif; ?>
                                <hr class="my-2">
                                <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="auth/login.php" class="bg-primary text-white px-6 py-2 rounded-full hover:bg-secondary transition">
                            Login
                        </a>
                        <a href="auth/register.php" class="bg-white text-primary border-2 border-primary px-6 py-2 rounded-full hover:bg-primary hover:text-white transition">
                            Sign Up
                        </a>
                    <?php endif; ?>
                    
                    <button id="mobile-menu-btn" class="md:hidden text-gray-700">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <a href="index.php" class="block py-2 text-gray-700 hover:text-primary">Home</a>
                <a href="customer/menu.php" class="block py-2 text-gray-700 hover:text-primary">Menu</a>
                <a href="about.php" class="block py-2 text-gray-700 hover:text-primary">About</a>
                <a href="contact.php" class="block py-2 text-gray-700 hover:text-primary">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1920&q=80" 
                 alt="Food Background" 
                 class="w-full h-full object-cover"
                 onerror="this.src='https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=1920&q=80'">
            <!-- Dark overlay for text readability -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/70 to-black/50"></div>
        </div>
        
        <!-- Content -->
        <div class="container mx-auto px-4 relative z-10 py-20">
            <div class="flex flex-col md:flex-row items-center justify-between gap-12">
                <!-- Left Content -->
                <div class="md:w-1/2 text-white space-y-5">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 bg-primary/90 backdrop-blur-sm px-4 py-2 rounded-full -mt-24">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        <span class="text-sm font-semibold">Now accepting orders • Fast delivery in 30 min</span>
                    </div>
                    
                    <!-- Main Heading -->
                    <h1 class="text-5xl md:text-7xl font-bold leading-tight">
                        Experience The
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-primary to-yellow-400">
                            Art of Flavor
                        </span>
                    </h1>
                    
                    <!-- Description -->
                    <p class="text-xl md:text-2xl text-gray-200 leading-relaxed max-w-xl">
                        Discover exquisite dishes crafted with passion. Fresh ingredients, 
                        authentic recipes, and unforgettable taste delivered right to your door.
                    </p>
                    
                    <!-- Features List -->
                    <div class="flex flex-wrap gap-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-primary/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                <i class="fas fa-star text-primary"></i>
                            </div>
                            <div>
                                <div class="font-bold text-lg">4.9/5</div>
                                <div class="text-sm text-gray-300">Rating</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-primary/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                <i class="fas fa-utensils text-primary"></i>
                            </div>
                            <div>
                                <div class="font-bold text-lg">50+</div>
                                <div class="text-sm text-gray-300">Dishes</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-primary/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                            <div>
                                <div class="font-bold text-lg">1000+</div>
                                <div class="text-sm text-gray-300">Customers</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <?php if (!is_admin()): ?>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <!-- Logged in user - direct to menu -->
                                <a href="customer/menu.php" class="group relative inline-flex items-center justify-center px-8 py-4 font-bold text-white bg-primary rounded-full overflow-hidden shadow-2xl transition-all duration-300 hover:scale-105 hover:shadow-primary/50">
                                    <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-56 group-hover:h-56 opacity-10"></span>
                                    <i class="fas fa-shopping-bag mr-2 relative"></i>
                                    <span class="relative">Order Now</span>
                                </a>
                            <?php else: ?>
                                <!-- Guest user - show notification first -->
                                <button onclick="showLoginPrompt()" class="group relative inline-flex items-center justify-center px-8 py-4 font-bold text-white bg-primary rounded-full overflow-hidden shadow-2xl transition-all duration-300 hover:scale-105 hover:shadow-primary/50">
                                    <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-56 group-hover:h-56 opacity-10"></span>
                                    <i class="fas fa-shopping-bag mr-2 relative"></i>
                                    <span class="relative">Order Now</span>
                                </button>
                            <?php endif; ?>
                        <a href="customer/menu.php" class="inline-flex items-center justify-center px-8 py-4 font-bold text-white border-2 border-white/50 backdrop-blur-sm rounded-full hover:bg-white hover:text-gray-900 transition-all duration-300 hover:scale-105">
                            <i class="fas fa-book-open mr-2"></i>
                            Explore Menu
                        </a>
                        <?php else: ?>
                        <a href="customer/menu.php" class="group relative inline-flex items-center justify-center px-8 py-4 font-bold text-white bg-gray-600 rounded-full overflow-hidden shadow-2xl transition-all duration-300 hover:scale-105">
                            <i class="fas fa-eye mr-2 relative"></i>
                            <span class="relative">View Menu</span>
                        </a>
                        <div class="inline-flex items-center justify-center px-8 py-4 font-bold text-gray-400 border-2 border-gray-500 backdrop-blur-sm rounded-full cursor-not-allowed">
                            <i class="fas fa-lock mr-2"></i>
                            Admin View Only
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Right Content - Floating Food Cards -->
                <div class="md:w-1/2 relative hidden md:block">
                    <div class="relative h-[600px]">
                        <!-- Card 1 -->
                        <div class="absolute top-0 right-0 w-72 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-4 transform hover:scale-105 transition-all duration-300 animate-float">
                            <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&q=80" 
                                 alt="Burger" 
                                 class="w-full h-40 object-cover rounded-xl mb-3">
                            <h3 class="text-xl font-semibold text-gray-800 text-lg mb-1">Premium Burger</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-primary font-bold text-xl">₱350.00</span>
                                <div class="flex items-center gap-1 text-yellow-400">
                                    <i class="fas fa-star text-sm"></i>
                                    <span class="text-gray-700 text-sm font-semibold">4.8</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Card 2 -->
                        <div class="absolute top-40 left-0 w-64 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-4 transform hover:scale-105 transition-all duration-300 animate-float-delay">
                            <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&q=80" 
                                 alt="Pizza" 
                                 class="w-full h-36 object-cover rounded-xl mb-3">
                            <h3 class="font-bold text-gray-800 text-lg mb-1">Italian Pizza</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-primary font-bold text-xl">₱420.00</span>
                                <div class="flex items-center gap-1 text-yellow-400">
                                    <i class="fas fa-star text-sm"></i>
                                    <span class="text-gray-700 text-sm font-semibold">4.9</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Card 3 -->
                        <div class="absolute bottom-0 right-12 w-60 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-4 transform hover:scale-105 transition-all duration-300 animate-float">
                            <img src="https://images.unsplash.com/photo-1612874742237-6526221588e3?w=400&q=80" 
                                 alt="Pasta" 
                                 class="w-full h-32 object-cover rounded-xl mb-3">
                            <h3 class="font-bold text-gray-800 text-lg mb-1">Pasta Carbonara</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-primary font-bold text-xl">₱320.00</span>
                                <div class="flex items-center gap-1 text-yellow-400">
                                    <i class="fas fa-star text-sm"></i>
                                    <span class="text-gray-700 text-sm font-semibold">4.7</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-10 animate-bounce">
            <div class="w-8 h-12 border-2 border-white/50 rounded-full flex items-start justify-center p-2">
                <div class="w-1 h-3 bg-white rounded-full"></div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Browse by Category</h2>
                <p class="text-gray-600 text-lg">Explore our diverse menu categories</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                <?php if (!is_admin()): ?>
                <a href="customer/menu.php?category=<?php echo $category['id']; ?>" class="card-hover bg-white rounded-xl shadow-lg p-6 text-center cursor-pointer transform transition-all duration-300">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center shadow-lg">
                        <i class="fas fa-utensils text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($category['name']); ?></h3>
                    <p class="text-gray-600"><?php echo htmlspecialchars($category['description']); ?></p>
                </a>
                <?php else: ?>
                <div class="bg-white rounded-xl shadow-lg p-6 text-center opacity-75 cursor-not-allowed">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-300 rounded-full flex items-center justify-center">
                        <i class="fas fa-utensils text-3xl text-gray-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2"><?php echo htmlspecialchars($category['name']); ?></h3>
                    <p class="text-gray-500"><?php echo htmlspecialchars($category['description']); ?></p>
                </div>
                <?php endif; ?>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Featured Dishes Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Popular Dishes</h2>
                <p class="text-gray-600 text-lg">Try our customer favorites</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php while ($item = mysqli_fetch_assoc($featured_result)): ?>
                <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden">
                    <?php 
                    // Check if image is a URL or filename
                    $image_src = $item['image'];
                    if (!empty($image_src) && !filter_var($image_src, FILTER_VALIDATE_URL)) {
                        $image_src = 'assets/images/food/' . $image_src;
                    }
                    ?>
                    <img src="<?php echo htmlspecialchars($image_src); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                         class="w-full h-48 object-cover"
                         onerror="this.src='https://via.placeholder.com/400x300?text=<?php echo urlencode($item['name']); ?>'">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <span class="text-primary font-bold text-xl"><?php echo format_price($item['price']); ?></span>
                        </div>
                        <p class="text-gray-600 mb-2 text-sm"><?php echo htmlspecialchars($item['category_name']); ?></p>
                        <p class="text-gray-700 mb-4"><?php echo htmlspecialchars($item['description']); ?></p>
                        <?php if (!is_admin()): ?>
                        <button onclick="addToCart(<?php echo $item['id']; ?>)" class="w-full bg-gradient-to-r from-primary to-secondary text-white py-3 rounded-lg hover:shadow-lg transition-all duration-300 transform hover:scale-105 font-semibold">
                            <i class="fas fa-cart-plus mr-2"></i>Add to Cart
                        </button>
                        <?php else: ?>
                        <div class="w-full bg-gray-300 text-gray-600 py-3 rounded-lg text-center font-semibold cursor-not-allowed">
                            <i class="fas fa-lock mr-2"></i>Admin View Only
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
            <?php if (!is_admin()): ?>
            <div class="text-center mt-12">
                <a href="customer/menu.php" class="inline-block bg-gradient-to-r from-primary to-secondary text-white px-8 py-3 rounded-full font-semibold hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-utensils mr-2"></i>View Full Menu
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Why Choose Gourmet Sentinel?</h2>
                <p class="text-gray-600 text-lg">We're committed to delivering the best experience</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-primary rounded-full flex items-center justify-center">
                        <i class="fas fa-leaf text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Fresh Ingredients</h3>
                    <p class="text-gray-600">We use only the freshest, locally-sourced ingredients for all our dishes</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-primary rounded-full flex items-center justify-center">
                        <i class="fas fa-shipping-fast text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Fast Delivery</h3>
                    <p class="text-gray-600">Hot food delivered to your door in 30 minutes or less</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-primary rounded-full flex items-center justify-center">
                        <i class="fas fa-star text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Quality Service</h3>
                    <p class="text-gray-600">Exceptional customer service and satisfaction guaranteed</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-100 text-gray-800 py-12 border-t border-gray-200">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <i class="fas fa-utensils text-2xl text-primary"></i>
                        <span class="text-2xl font-bold text-gray-800">Gourmet Sentinel</span>
                    </div>
                    <p class="text-gray-600">Delicious food delivered to your door. Fresh, fast, and affordable.</p>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-gray-800">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-600 hover:text-primary transition-colors">Home</a></li>
                        <li><a href="customer/menu.php" class="text-gray-600 hover:text-primary transition-colors">Menu</a></li>
                        <li><a href="about.php" class="text-gray-600 hover:text-primary transition-colors">About Us</a></li>
                        <li><a href="contact.php" class="text-gray-600 hover:text-primary transition-colors">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-gray-800">Customer Service</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 hover:text-primary transition-colors">Track Order</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-primary transition-colors">Terms & Conditions</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-primary transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-primary transition-colors">FAQ</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-gray-800">Contact Us</h4>
                    <ul class="space-y-2 text-gray-600">
                        <li><i class="fas fa-phone mr-2 text-primary"></i>+63 123 456 7890</li>
                        <li><i class="fas fa-envelope mr-2 text-primary"></i>info@gourmetsentinel.com</li>
                        <li><i class="fas fa-map-marker-alt mr-2 text-primary"></i>Panabo City, Davao del Norte</li>
                    </ul>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center hover:bg-secondary transition-all"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center hover:bg-secondary transition-all"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center hover:bg-secondary transition-all"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-300 mt-8 pt-8 text-center text-gray-600">
                <p>&copy; 2025 Gourmet Sentinel. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollToTop" class="fixed bottom-8 right-8 bg-primary text-white w-12 h-12 rounded-full shadow-lg hover:bg-secondary transition-all duration-300 opacity-0 invisible z-50 flex items-center justify-center">
        <i class="fas fa-arrow-up text-xl"></i>
    </button>

    <script>
        // User menu dropdown toggle
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userMenuDropdown = document.getElementById('userMenuDropdown');
            
            if (userMenuBtn && userMenuDropdown) {
                userMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenuDropdown.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userMenuBtn.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                        userMenuDropdown.classList.add('hidden');
                    }
                });
            }
        });
        
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Show login prompt for guest users
        function showLoginPrompt() {
            // Create custom modal notification
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm';
            modal.innerHTML = `
                <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md mx-4 transform animate-scale-in">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-lock text-4xl text-primary"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-3">Login Required</h3>
                        <p class="text-gray-600 mb-6">Please login to your account to start ordering delicious food!</p>
                        <div class="flex gap-3">
                            <button onclick="closeLoginPrompt()" class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-full font-semibold hover:bg-gray-100 transition-all">
                                Cancel
                            </button>
                            <a href="auth/login.php" class="flex-1 px-6 py-3 bg-gradient-to-r from-primary to-secondary text-white rounded-full font-semibold hover:shadow-lg transition-all text-center">
                                Login Now
                            </a>
                        </div>
                        <div class="mt-4 text-sm text-gray-600">
                            Don't have an account? <a href="auth/register.php" class="text-primary font-semibold hover:underline">Sign up</a>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            
            // Add animation style
            const style = document.createElement('style');
            style.textContent = `
                @keyframes scale-in {
                    from {
                        transform: scale(0.9);
                        opacity: 0;
                    }
                    to {
                        transform: scale(1);
                        opacity: 1;
                    }
                }
                .animate-scale-in {
                    animation: scale-in 0.3s ease-out;
                }
            `;
            document.head.appendChild(style);
            
            // Close on background click
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeLoginPrompt();
                }
            });
        }

        function closeLoginPrompt() {
            const modal = document.querySelector('.fixed.inset-0.z-50');
            if (modal) {
                modal.remove();
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

        // Add to cart function
        function addToCart(itemId) {
            fetch('customer/cart_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=add&item_id=' + itemId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Item added to cart!', 'success');
                    updateCartCount();
                } else {
                    showNotification(data.message || 'Failed to add item', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }

        // Update cart count
        function updateCartCount() {
            fetch('customer/get_cart_count.php')
                .then(response => response.json())
                .then(data => {
                    location.reload(); // Reload to update cart count
                });
        }

        // Show notification
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
