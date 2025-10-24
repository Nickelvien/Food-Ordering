<?php
session_start();
require_once '../db.php';

// Redirect if already logged in
if (is_logged_in()) {
    if (is_admin()) {
        redirect('../admin/dashboard.php');
    } else {
        redirect('../customer/menu.php');
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'All fields are required';
    } else {
        // Query to check user credentials
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    redirect('../admin/dashboard.php');
                } else {
                    redirect('../customer/menu.php');
                }
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gourmet Sentinel</title>
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
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            position: relative;
        }
        
        /* Autoscrolling Background */
        .bg-slideshow {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
        
        .bg-slideshow::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 2;
        }
        
        .bg-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            animation: slideShow 20s infinite;
        }
        
        .bg-slide:nth-child(1) { animation-delay: 0s; }
        .bg-slide:nth-child(2) { animation-delay: 5s; }
        .bg-slide:nth-child(3) { animation-delay: 10s; }
        .bg-slide:nth-child(4) { animation-delay: 15s; }
        
        @keyframes slideShow {
            0% { opacity: 0; transform: scale(1); }
            5% { opacity: 1; transform: scale(1.05); }
            25% { opacity: 1; transform: scale(1.1); }
            30% { opacity: 0; transform: scale(1.15); }
            100% { opacity: 0; transform: scale(1); }
        }
        
        /* Pattern Overlay */
        .pattern-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="white" opacity="0.1"/></svg>');
            background-size: 50px 50px;
            z-index: 1;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <!-- Autoscrolling Background Slideshow -->
    <div class="bg-slideshow">
        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1920&q=80" alt="Food Background" class="bg-slide">
        <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=1920&q=80" alt="Pizza Background" class="bg-slide">
        <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=1920&q=80" alt="Breakfast Background" class="bg-slide">
        <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=1920&q=80" alt="Burger Background" class="bg-slide">
    </div>
    
    <!-- Pattern Overlay -->
    <div class="pattern-overlay"></div>
    
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-md w-full">
            <!-- Logo and Title with Enhanced Design -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center space-x-3 mb-6 bg-white/10 backdrop-blur-lg px-8 py-4 rounded-2xl shadow-2xl">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <i class="fas fa-utensils text-4xl text-primary"></i>
                    </div>
                    <span class="text-4xl font-bold text-white">Gourmet Sentinel</span>
                </div>
                <h2 class="text-4xl font-bold text-white mb-3 drop-shadow-lg">Welcome Back!</h2>
                <p class="text-gray-100 text-lg">Sign in to your account to continue</p>
            </div>

            <!-- Login Form Card with Glass Effect -->
            <div class="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl p-10 transform hover:scale-105 transition-all duration-300">
                <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span><?php echo htmlspecialchars($success); ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-envelope mr-2 text-primary"></i>Email Address
                        </label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Enter your email">
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-lock mr-2 text-primary"></i>Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="Enter your password">
                            <button type="button" onclick="togglePassword()" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-primary transition-colors">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="#" class="text-sm text-primary hover:text-secondary">Forgot password?</a>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-primary to-secondary text-white py-4 rounded-xl font-bold text-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Sign In</span>
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Don't have an account? 
                        <a href="register.php" class="text-primary hover:text-secondary font-semibold">Sign up now</a>
                    </p>
                </div>

                <!-- Demo Credentials -->
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800 font-semibold mb-2">Demo Credentials:</p>
                    <div class="text-xs text-blue-700 space-y-1">
                        <p><strong>Admin:</strong> admin@foodhub.com / admin123</p>
                        <p><strong>Customer:</strong> customer@example.com / customer123</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-6">
                <a href="../index.php" class="text-gray-600 hover:text-primary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
    
    <script>
        // Drag functionality for floating food cards
        const floatingCards = document.querySelectorAll('.floating-food');
        
        floatingCards.forEach(card => {
            let isDragging = false;
            let currentX;
            let currentY;
            let initialX;
            let initialY;
            let xOffset = 0;
            let yOffset = 0;

            card.addEventListener('mousedown', dragStart);
            card.addEventListener('mouseup', dragEnd);
            card.addEventListener('mousemove', drag);
            card.addEventListener('touchstart', dragStart);
            card.addEventListener('touchend', dragEnd);
            card.addEventListener('touchmove', drag);

            function dragStart(e) {
                if (e.type === 'touchstart') {
                    initialX = e.touches[0].clientX - xOffset;
                    initialY = e.touches[0].clientY - yOffset;
                } else {
                    initialX = e.clientX - xOffset;
                    initialY = e.clientY - yOffset;
                }

                if (e.target === card || card.contains(e.target)) {
                    isDragging = true;
                    card.style.animation = 'none';
                }
            }

            function dragEnd(e) {
                initialX = currentX;
                initialY = currentY;
                isDragging = false;
            }

            function drag(e) {
                if (isDragging) {
                    e.preventDefault();
                    
                    if (e.type === 'touchmove') {
                        currentX = e.touches[0].clientX - initialX;
                        currentY = e.touches[0].clientY - initialY;
                    } else {
                        currentX = e.clientX - initialX;
                        currentY = e.clientY - initialY;
                    }

                    xOffset = currentX;
    </script>
</body>
</html>