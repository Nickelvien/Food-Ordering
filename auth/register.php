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
    $full_name = sanitize_input($_POST['full_name']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = sanitize_input($_POST['phone']);
    
    // Validation
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if email already exists
        $check_query = "SELECT id FROM users WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Email already registered';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $insert_query = "INSERT INTO users (full_name, email, password, phone, role) 
                           VALUES ('$full_name', '$email', '$hashed_password', '$phone', 'customer')";
            
            if (mysqli_query($conn, $insert_query)) {
                $success = 'Registration successful! You can now login.';
                // Optionally redirect after 2 seconds
                header("refresh:2;url=login.php");
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Gourmet Sentinel</title>
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
                <h2 class="text-4xl font-bold text-white mb-3 drop-shadow-lg">Create Account</h2>
                <p class="text-gray-100 text-lg">Join us and start ordering delicious food</p>
            </div>

            <!-- Registration Form Card with Glass Effect -->
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

                <form method="POST" action="register.php">
                    <div class="mb-4">
                        <label for="full_name" class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-user mr-2 text-primary"></i>Full Name *
                        </label>
                        <input type="text" id="full_name" name="full_name" required
                               value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Enter your full name">
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-envelope mr-2 text-primary"></i>Email Address *
                        </label>
                        <input type="email" id="email" name="email" required
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Enter your email">
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-phone mr-2 text-primary"></i>Phone Number
                        </label>
                        <input type="tel" id="phone" name="phone" pattern="[0-9]{10,11}" maxlength="11"
                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="09XX XXX XXXX" title="Please enter 10-11 digit phone number"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-lock mr-2 text-primary"></i>Password *
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="Enter your password (min. 6 characters)">
                            <button type="button" onclick="togglePassword('password', 'togglePasswordIcon')" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                                <i id="togglePasswordIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="confirm_password" class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-lock mr-2 text-primary"></i>Confirm Password *
                        </label>
                        <div class="relative">
                            <input type="password" id="confirm_password" name="confirm_password" required
                                   class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="Confirm your password">
                            <button type="button" onclick="togglePassword('confirm_password', 'toggleConfirmPasswordIcon')" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                                <i id="toggleConfirmPasswordIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" required class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-600">
                                I agree to the <a href="#" class="text-primary hover:text-secondary">Terms & Conditions</a>
                            </span>
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-primary to-secondary text-white py-4 rounded-xl font-bold text-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2">
                        <i class="fas fa-user-plus"></i>
                        <span>Create Account</span>
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Already have an account? 
                        <a href="login.php" class="text-primary hover:text-secondary font-bold">Sign in</a>
                    </p>
                </div>
            </div>

            <div class="text-center mt-6">
                <a href="../index.php" class="text-white hover:text-gray-100 font-semibold inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-6 py-3 rounded-full transition-all duration-300 hover:bg-white/20">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Home</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
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

        // Password strength indicator (optional enhancement)
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strength = document.getElementById('password-strength');
            
            if (password.length < 6) {
                this.classList.add('border-red-300');
                this.classList.remove('border-green-300');
            } else {
                this.classList.remove('border-red-300');
                this.classList.add('border-green-300');
            }
        });

        // Password match indicator
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword && confirmPassword.length > 0) {
                this.classList.add('border-red-300');
                this.classList.remove('border-green-300');
            } else if (confirmPassword.length > 0) {
                this.classList.remove('border-red-300');
                this.classList.add('border-green-300');
            }
        });
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