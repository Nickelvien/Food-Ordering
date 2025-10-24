<?php
session_start();
require_once 'db.php';
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $subject = sanitize_input($_POST['subject'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email';
    } else {
        $success = 'Thank you! We will get back to you soon.';
        $name = $email = $subject = $message = '';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Gourmet Sentinel</title>
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
                <a href="index.php" class="text-gray-700 hover:text-primary transition">Home</a>
                <?php if (!is_admin()): ?>
                <a href="customer/menu.php" class="text-gray-700 hover:text-primary transition">Menu</a>
                <?php endif; ?>
                <a href="about.php" class="text-gray-700 hover:text-primary transition">About</a>
                <a href="contact.php" class="text-primary font-semibold">Contact</a>
            </div>
            <div class="flex items-center space-x-4">
                <?php if (isset($_SESSION['user_id'])): ?><a href="logout.php" class="bg-gradient-to-r from-primary to-secondary text-white px-6 py-2 rounded-full hover:shadow-lg transition">Logout</a><?php else: ?><a href="auth/login.php" class="text-gray-700 hover:text-primary transition">Login</a><a href="auth/register.php" class="bg-gradient-to-r from-primary to-secondary text-white px-6 py-2 rounded-full hover:shadow-lg transition">Sign Up</a><?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Minimized Professional Contact Hero -->
<section class="relative bg-cover bg-center text-white overflow-hidden" style="background-image: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), url('https://images.unsplash.com/photo-1423666639041-f56000c27a9a?w=1920&q=80'); min-height: 350px;">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 via-transparent to-purple-500/20"></div>
    
    <div class="container mx-auto px-4 relative z-10 py-20">
        <div class="max-w-3xl mx-auto text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-5 py-2 rounded-full mb-5 border border-white/20">
                <i class="fas fa-headset text-blue-300"></i>
                <span class="font-semibold text-sm">24/7 Support</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-5xl md:text-6xl font-bold mb-4 drop-shadow-2xl">
                Contact Us
            </h1>
            
            <p class="text-xl md:text-2xl font-light text-white/90 mb-6 max-w-2xl mx-auto">
                We'd love to hear from you! Get in touch with us
            </p>
            
            <!-- Quick Contact -->
            <div class="flex justify-center gap-4 flex-wrap text-sm">
                <a href="tel:+631234567890" class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-full flex items-center gap-2 hover:bg-white/20 transition border border-white/20">
                    <i class="fas fa-phone text-blue-300"></i>
                    <span>+63 123 456 7890</span>
                </a>
                <a href="mailto:info@gourmetsentinel.com" class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-full flex items-center gap-2 hover:bg-white/20 transition border border-white/20">
                    <i class="fas fa-envelope text-purple-300"></i>
                    <span>info@gourmetsentinel.com</span>
                </a>
            </div>
        </div>
    </div>
</section>
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 max-w-6xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Send a Message</h2>
                <?php if ($success): ?><div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6"><i class="fas fa-check-circle mr-2"></i> <?php echo $success; ?></div><?php endif; ?>
                <?php if ($error): ?><div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6"><i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error; ?></div><?php endif; ?>
                <form method="POST" class="space-y-6">
                    <div><label class="block text-gray-700 font-semibold mb-2"><i class="fas fa-user mr-2 text-primary"></i> Name</label><input type="text" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required></div>
                    <div><label class="block text-gray-700 font-semibold mb-2"><i class="fas fa-envelope mr-2 text-primary"></i> Email</label><input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required></div>
                    <div><label class="block text-gray-700 font-semibold mb-2"><i class="fas fa-tag mr-2 text-primary"></i> Subject</label><input type="text" name="subject" value="<?php echo htmlspecialchars($subject ?? ''); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required></div>
                    <div><label class="block text-gray-700 font-semibold mb-2"><i class="fas fa-comment mr-2 text-primary"></i> Message</label><textarea name="message" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required><?php echo htmlspecialchars($message ?? ''); ?></textarea></div>
                    <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary text-white px-8 py-4 rounded-full font-bold text-lg hover:shadow-2xl transition transform hover:scale-105"><i class="fas fa-paper-plane mr-2"></i> Send Message</button>
                </form>
            </div>
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Get in Touch</h2>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-phone text-white text-xl"></i></div>
                            <div><h3 class="font-bold text-gray-800 mb-1">Phone</h3><p class="text-gray-600">+63 123 456 7890</p></div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-envelope text-white text-xl"></i></div>
                            <div><h3 class="font-bold text-gray-800 mb-1">Email</h3><p class="text-gray-600">info@gourmetsentinel.com</p></div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-map-marker-alt text-white text-xl"></i></div>
                            <div><h3 class="font-bold text-gray-800 mb-1">Address</h3><p class="text-gray-600">Panabo City, Davao del Norte, Philippines</p></div>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-primary to-secondary rounded-2xl shadow-xl p-8 text-white">
                    <h3 class="text-2xl font-bold mb-4">Follow Us</h3>
                    <p class="mb-6">Stay connected for updates!</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition"><i class="fab fa-facebook-f text-xl"></i></a>
                        <a href="#" class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition"><i class="fab fa-instagram text-xl"></i></a>
                        <a href="#" class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition"><i class="fab fa-twitter text-xl"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<footer class="bg-gray-100 text-gray-800 py-12 border-t border-gray-200">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div><h3 class="text-2xl font-bold mb-4 text-gray-800"><i class="fas fa-utensils text-primary"></i> Gourmet Sentinel</h3><p class="text-gray-600">Delicious food delivered</p></div>
            <div><h4 class="font-bold mb-4 text-gray-800">Quick Links</h4><ul class="space-y-2"><li><a href="index.php" class="text-gray-600 hover:text-primary transition-colors">Home</a></li><li><a href="customer/menu.php" class="text-gray-600 hover:text-primary transition-colors">Menu</a></li></ul></div>
            <div><h4 class="font-bold mb-4 text-gray-800">Contact</h4><ul class="space-y-2 text-gray-600"><li><i class="fas fa-phone mr-2 text-primary"></i> +63 123 456 7890</li><li><i class="fas fa-envelope mr-2 text-primary"></i> info@gourmetsentinel.com</li><li><i class="fas fa-map-marker-alt mr-2 text-primary"></i> Panabo City, Davao del Norte</li></ul></div>
            <div><h4 class="font-bold mb-4 text-gray-800">Follow Us</h4><div class="flex space-x-4"><a href="#" class="w-10 h-10 bg-primary rounded-full flex items-center justify-center hover:bg-secondary transition-all text-white"><i class="fab fa-facebook-f"></i></a></div></div>
        </div>
        <div class="border-t border-gray-300 mt-8 pt-8 text-center text-gray-600"><p>&copy; 2025 Gourmet Sentinel. All rights reserved.</p></div>
    </div>
</footer>
</body>
</html>
