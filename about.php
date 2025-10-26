<?php
session_start();
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Gourmet Sentinel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/shared-styles.css">
    <script>tailwind.config = {theme: {extend: {colors: {primary: '#f59e0b', secondary: '#fb923c'}}}}</script>
</head>
<body class="bg-gray-50">
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
            </a>
            <div class="hidden md:flex space-x-8">
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

<!-- Minimized Professional About Hero -->
<section class="relative bg-cover bg-center text-white overflow-hidden" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1600565193348-f74bd3c7ccdf?w=1920&q=80'); min-height: 350px;">
    <div class="absolute inset-0 bg-gradient-to-br from-orange-500/20 via-transparent to-pink-500/20"></div>
    
    <div class="container mx-auto px-4 relative z-10 py-20">
        <div class="max-w-3xl mx-auto text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-5 py-2 rounded-full mb-5 border border-white/20">
                <i class="fas fa-award text-yellow-300"></i>
                <span class="font-semibold text-sm">Established 2020</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-5xl md:text-6xl font-bold mb-4 drop-shadow-2xl">
                About Us
            </h1>
            
            <p class="text-xl md:text-2xl font-light text-white/90 max-w-2xl mx-auto">
                Serving delicious food with passion and dedication
            </p>
        </div>
    </div>
</section>
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Our Story</h2>
                <div class="w-20 h-1 bg-gradient-to-r from-primary to-secondary mx-auto rounded-full"></div>
            </div>
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12">
                <p class="text-gray-700 text-lg leading-relaxed mb-6">Welcome to <span class="font-bold text-primary">Gourmet Sentinel</span>, where passion meets flavor! Founded in 2020, we bring the finest culinary experiences to your doorstep.</p>
                <p class="text-gray-700 text-lg leading-relaxed mb-6">Our journey began with a simple vision: to make quality food accessible to everyone. We believe great food brings people together and adds joy to everyday life.</p>
                <p class="text-gray-700 text-lg leading-relaxed">Every dish is crafted with fresh ingredients by skilled chefs who pour their hearts into every recipe.</p>
            </div>
        </div>
    </div>
</section>
<section class="py-16 bg-gray-100">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Our Values</h2>
            <div class="w-20 h-1 bg-gradient-to-r from-primary to-secondary mx-auto rounded-full"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-2xl transition transform hover:-translate-y-2">
                <div class="w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mx-auto mb-6"><i class="fas fa-star text-3xl text-white"></i></div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Quality First</h3>
                <p class="text-gray-600">We never compromise on quality. Every ingredient is carefully selected.</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-2xl transition transform hover:-translate-y-2">
                <div class="w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mx-auto mb-6"><i class="fas fa-leaf text-3xl text-white"></i></div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Fresh Ingredients</h3>
                <p class="text-gray-600">All dishes are made from fresh, locally-sourced ingredients delivered daily.</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-2xl transition transform hover:-translate-y-2">
                <div class="w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mx-auto mb-6"><i class="fas fa-heart text-3xl text-white"></i></div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Customer Love</h3>
                <p class="text-gray-600">Your satisfaction is our priority with exceptional service every time.</p>
            </div>
        </div>
    </div>
</section>
<footer class="bg-gray-100 text-gray-800 py-12 border-t border-gray-200">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div><h3 class="text-2xl font-bold mb-4 text-gray-800"><i class="fas fa-utensils text-primary"></i> Gourmet Sentinel</h3><p class="text-gray-600">Delicious food delivered</p></div>
            <div><h4 class="font-bold mb-4 text-gray-800">Quick Links</h4><ul class="space-y-2"><li><a href="index.php" class="text-gray-600 hover:text-primary transition-colors">Home</a></li><li><a href="customer/menu.php" class="text-gray-600 hover:text-primary transition-colors">Menu</a></li></ul></div>
            <div><h4 class="font-bold mb-4 text-gray-800">Contact</h4><ul class="space-y-2 text-gray-600"><li><i class="fas fa-phone mr-2 text-primary"></i> +63 123 456 7890</li><li><i class="fas fa-envelope mr-2 text-primary"></i> info@gourmetsentinel.com</li></ul></div>
            <div><h4 class="font-bold mb-4 text-gray-800">Follow Us</h4><div class="flex space-x-4"><a href="#" class="w-10 h-10 bg-primary rounded-full flex items-center justify-center hover:bg-secondary transition-all text-white"><i class="fab fa-facebook-f"></i></a></div></div>
        </div>
        <div class="border-t border-gray-300 mt-8 pt-8 text-center text-gray-600"><p>&copy; 2024 Gourmet Sentinel. All rights reserved.</p></div>
    </div>
</footer>

<script>
// User Dropdown Toggle
document.getElementById('userMenuBtn')?.addEventListener('click', function(e) {
    e.stopPropagation();
    const dropdown = document.getElementById('userMenuDropdown');
    dropdown.classList.toggle('hidden');
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('userMenuDropdown');
    const userMenuBtn = document.getElementById('userMenuBtn');
    if (dropdown && userMenuBtn && !dropdown.contains(e.target) && !userMenuBtn.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});

// Mobile Menu Toggle
document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
    const mobileMenu = document.getElementById('mobile-menu');
    mobileMenu.classList.toggle('hidden');
});

// Close mobile menu when a link is clicked
document.querySelectorAll('#mobile-menu a').forEach(link => {
    link.addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.add('hidden');
    });
});
</script>
</body>
</html>
