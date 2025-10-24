<?php
session_start();
require_once '../db.php';

$cart = get_cart();
$cart_items = [];
$total = 0;

// Fetch cart items details
if (!empty($cart)) {
    $item_ids = implode(',', array_keys($cart));
    $query = "SELECT * FROM food_items WHERE id IN ($item_ids) AND is_available = 1";
    $result = mysqli_query($conn, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $row['quantity'] = $cart[$row['id']];
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $total += $row['subtotal'];
        $cart_items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Gourmet Sentinel</title>
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
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Modern Logo Design -->
                <a href="../index.php" class="flex items-center space-x-3 group">
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
                
                <div class="flex items-center space-x-4">
                    <a href="menu.php" class="text-gray-700 hover:text-primary">
                        <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header with Enhanced Design -->
    <div class="page-header text-white py-16">
        <div class="container mx-auto px-4 relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold mb-2">Shopping Cart</h1>
                    <p class="text-xl text-gray-100">Review your items before checkout</p>
                </div>
                <div class="hidden md:block">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-4xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Content -->
    <div class="container mx-auto px-4 py-8">        
        <?php if (empty($cart_items)): ?>
        <div class="modern-card text-center py-16 shadow-primary-lg max-w-2xl mx-auto">
            <div class="relative inline-block mb-6">
                <div class="w-32 h-32 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center mx-auto shadow-inner">
                    <i class="fas fa-shopping-cart text-6xl text-orange-400"></i>
                </div>
                <div class="absolute -top-2 -right-2 w-16 h-16 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-heart-broken text-white text-2xl"></i>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-3">Your cart is empty</h2>
            <p class="text-gray-600 mb-8 text-lg max-w-md mx-auto">Looks like you haven't added anything to your cart yet. Browse our delicious menu!</p>
            <a href="menu.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-primary to-secondary text-white px-10 py-4 rounded-full hover:shadow-2xl transition-all duration-300 transform hover:scale-105 font-semibold text-lg">
                <i class="fas fa-utensils"></i>
                Browse Menu
            </a>
        </div>
        <?php else: ?>
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Cart Items with Enhanced Design -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Header Card -->
                <div class="bg-gradient-to-r from-primary to-secondary rounded-2xl p-1 shadow-xl">
                    <div class="bg-white rounded-xl p-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-shopping-basket text-white text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">Your Cart</h2>
                                    <p class="text-sm text-gray-600"><?php echo count($cart_items); ?> delicious items selected</p>
                                </div>
                            </div>
                            <button onclick="clearCart()" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-all duration-300 font-semibold text-sm border border-red-200">
                                <i class="fas fa-trash-alt mr-2"></i>Clear All
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Cart Items List -->
                <div class="space-y-3">
                <?php foreach ($cart_items as $index => $item): ?>
                <div class="group bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100" id="cart-item-<?php echo $item['id']; ?>">
                    <div class="p-5">
                        <div class="flex items-start gap-5">
                            <!-- Product Image with Badge -->
                            <div class="relative flex-shrink-0">
                                <div class="absolute -top-2 -left-2 w-8 h-8 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg z-10">
                                    <?php echo $index + 1; ?>
                                </div>
                                <?php 
                                $image_src = $item['image'];
                                if (!empty($image_src) && !filter_var($image_src, FILTER_VALIDATE_URL)) {
                                    $image_src = '../assets/images/food/' . $image_src;
                                }
                                ?>
                                <div class="relative overflow-hidden rounded-2xl shadow-lg group-hover:shadow-2xl transition-shadow">
                                    <img src="<?php echo htmlspecialchars($image_src); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         class="w-32 h-32 object-cover transform group-hover:scale-110 transition-transform duration-500"
                                         onerror="this.src='https://via.placeholder.com/128?text=Food'">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                </div>
                            </div>
                            
                            <!-- Product Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-primary transition-colors">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </h3>
                                        <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?php echo htmlspecialchars($item['description']); ?></p>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-gray-500">Price per item:</span>
                                            <span class="text-lg font-bold text-primary"><?php echo format_price($item['price']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Quantity and Total Section -->
                                <div class="flex items-center justify-between gap-4 pt-3 border-t border-gray-100">
                                    <!-- Quantity Controls -->
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-semibold text-gray-600">Quantity:</span>
                                        <div class="flex items-center bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl shadow-inner border border-gray-200">
                                            <button onclick="updateQuantity(<?php echo $item['id']; ?>, -1)" 
                                                    class="w-10 h-10 flex items-center justify-center hover:bg-primary hover:text-white rounded-l-xl transition-all duration-300 group/btn">
                                                <i class="fas fa-minus text-sm"></i>
                                            </button>
                                            <span class="w-16 h-10 flex items-center justify-center font-bold text-gray-800 bg-white border-x border-gray-200 text-lg" id="qty-<?php echo $item['id']; ?>">
                                                <?php echo $item['quantity']; ?>
                                            </span>
                                            <button onclick="updateQuantity(<?php echo $item['id']; ?>, 1)" 
                                                    class="w-10 h-10 flex items-center justify-center hover:bg-primary hover:text-white rounded-r-xl transition-all duration-300 group/btn">
                                                <i class="fas fa-plus text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Subtotal and Remove -->
                                    <div class="flex items-center gap-4">
                                        <div class="text-right">
                                            <div class="text-xs text-gray-500 mb-1">Item Total</div>
                                            <div class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent" id="subtotal-<?php echo $item['id']; ?>">
                                                <?php echo format_price($item['subtotal']); ?>
                                            </div>
                                        </div>
                                        <button onclick="removeItem(<?php echo $item['id']; ?>)" 
                                                class="flex items-center gap-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-4 py-3 rounded-xl transition-all duration-300 font-semibold text-sm border border-red-200 hover:border-red-600">
                                            <i class="fas fa-trash-alt"></i>
                                            <span class="hidden sm:inline">Remove</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Order Summary - Professional UI/UX Design -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-4">
                    <!-- Main Summary Card -->
                    <div class="bg-white rounded-2xl shadow-xl border-2 border-gray-100 overflow-hidden">
                        <!-- Header with Icon -->
                        <div class="bg-gradient-to-r from-primary to-secondary p-4">
                            <div class="flex items-center justify-between text-white">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                        <i class="fas fa-file-invoice text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg">Order Summary</h3>
                                        <p class="text-xs text-white/80"><?php echo count($cart_items); ?> item(s)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Summary Details -->
                        <div class="p-5 space-y-4">
                            <!-- Subtotal -->
                            <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                <div class="flex items-center gap-2 text-gray-600">
                                    <i class="fas fa-shopping-basket text-primary"></i>
                                    <span class="font-medium">Subtotal</span>
                                </div>
                                <span class="text-xl font-bold text-gray-800" id="cart-subtotal"><?php echo format_price($total); ?></span>
                            </div>
                            
                            <!-- Total Amount - Highlighted -->
                            <div class="bg-gradient-to-br from-orange-50 to-amber-50 p-4 rounded-xl border-2 border-primary/30">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="text-sm text-gray-600 mb-1">Total to Pay</div>
                                        <div class="text-3xl font-black bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent" id="cart-total">
                                            <?php echo format_price($total); ?>
                                        </div>
                                    </div>
                                    <div class="w-14 h-14 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center shadow-lg animate-pulse">
                                        <i class="fas fa-peso-sign text-white text-2xl"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Methods -->
                            <div class="bg-green-50 p-4 rounded-xl border border-green-200">
                                <div class="flex items-center gap-2 mb-3">
                                    <i class="fas fa-credit-card text-green-600"></i>
                                    <span class="text-sm font-bold text-green-800">Payment Options</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="bg-white p-3 rounded-lg border border-green-300 text-center hover:border-green-500 transition-all cursor-pointer">
                                        <i class="fas fa-truck text-green-600 text-lg mb-1"></i>
                                        <div class="text-xs font-semibold text-gray-700">Cash on Delivery</div>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg border border-orange-300 text-center hover:border-orange-500 transition-all cursor-pointer">
                                        <i class="fas fa-store text-orange-600 text-lg mb-1"></i>
                                        <div class="text-xs font-semibold text-gray-700">Pay at Counter</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons - Professional Design -->
                    <div class="space-y-3">
                        <?php if (is_logged_in()): ?>
                        <!-- Checkout Button -->
                        <a href="checkout.php" class="group block w-full bg-gradient-to-r from-primary to-secondary text-white text-center py-4 rounded-xl font-bold hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02] relative overflow-hidden">
                            <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>
                            <div class="relative flex items-center justify-center gap-2">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Proceed to Checkout</span>
                                <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </a>
                        
                        <!-- Continue Shopping -->
                        <a href="menu.php" class="block w-full bg-white text-primary text-center py-3 rounded-xl font-semibold border-2 border-primary hover:bg-orange-50 transition-all duration-300">
                            <i class="fas fa-plus-circle mr-2"></i>Continue Shopping
                        </a>
                        <?php else: ?>
                        <!-- Login Button -->
                        <a href="../auth/login.php?redirect=cart" class="group block w-full bg-gradient-to-r from-primary to-secondary text-white text-center py-4 rounded-xl font-bold hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02] relative overflow-hidden">
                            <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>
                            <div class="relative flex items-center justify-center gap-2">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login to Checkout</span>
                            </div>
                        </a>
                        <?php endif; ?>
                        
                        <!-- Trust Indicators - Enhanced Visibility -->
                        <div class="bg-white p-5 rounded-xl border-2 border-gray-200 shadow-sm">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center mb-2 shadow-md">
                                        <i class="fas fa-shield-alt text-green-600 text-lg"></i>
                                    </div>
                                    <span class="text-xs font-bold text-gray-800">Secure</span>
                                    <span class="text-[10px] text-gray-500">Protected</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mb-2 shadow-md">
                                        <i class="fas fa-headset text-blue-600 text-lg"></i>
                                    </div>
                                    <span class="text-xs font-bold text-gray-800">Support</span>
                                    <span class="text-[10px] text-gray-500">24/7 Help</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center mb-2 shadow-md">
                                        <i class="fas fa-shipping-fast text-primary text-lg"></i>
                                    </div>
                                    <span class="text-xs font-bold text-gray-800">Fast</span>
                                    <span class="text-[10px] text-gray-500">Delivery</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function updateQuantity(itemId, change) {
            const qtyElement = document.getElementById('qty-' + itemId);
            let currentQty = parseInt(qtyElement.textContent);
            let newQty = currentQty + change;
            
            if (newQty < 1) return;
            
            fetch('cart_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update&item_id=${itemId}&quantity=${newQty}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function removeItem(itemId) {
            if (!confirm('Remove this item from cart?')) return;
            
            fetch('cart_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove&item_id=${itemId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function clearCart() {
            if (!confirm('Are you sure you want to clear your cart?')) return;
            
            fetch('cart_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=clear'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>
