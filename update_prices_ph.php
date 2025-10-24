<?php
/**
 * Update Product Prices to Philippine Market Rates
 * Run this once: http://localhost/Food_Ordering/update_prices_ph.php
 */

require_once 'db.php';

// Philippine Peso pricing (realistic market rates for gourmet/restaurant)
$price_updates = [
    // Burgers (₱250-450)
    'Classic Beef Burger' => 285.00,
    'Chicken Burger' => 265.00,
    'Veggie Burger' => 240.00,
    'Double Cheese Burger' => 380.00,
    
    // Pasta (₱280-420)
    'Spaghetti Carbonara' => 320.00,
    'Penne Arrabbiata' => 295.00,
    'Fettuccine Alfredo' => 310.00,
    'Lasagna' => 385.00,
    
    // Drinks (₱80-150)
    'Fresh Orange Juice' => 120.00,
    'Mango Smoothie' => 145.00,
    'Iced Coffee' => 95.00,
    'Lemonade' => 85.00,
    
    // Desserts (₱180-250)
    'Chocolate Cake' => 220.00,
    'Tiramisu' => 245.00,
    'Ice Cream Sundae' => 185.00,
    'Cheesecake' => 235.00,
    
    // Pizza (₱350-480)
    'Margherita Pizza' => 365.00,
    'Pepperoni Pizza' => 425.00,
    'Vegetarian Pizza' => 380.00,
    
    // Salads (₱180-250)
    'Caesar Salad' => 225.00,
    'Greek Salad' => 240.00,
];

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Update Prices - Gourmet Sentinel</title>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body class='bg-gray-100 p-8'>
    <div class='max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8'>
        <h1 class='text-3xl font-bold text-gray-800 mb-6'>Philippine Peso Price Update</h1>
        <div class='space-y-3'>";

$updated_count = 0;
$failed_count = 0;

foreach ($price_updates as $product_name => $new_price) {
    $safe_name = mysqli_real_escape_string($conn, $product_name);
    
    // Update the price
    $update_query = "UPDATE food_items SET price = $new_price WHERE name = '$safe_name'";
    
    if (mysqli_query($conn, $update_query)) {
        if (mysqli_affected_rows($conn) > 0) {
            echo "<div class='flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded'>
                    <span class='font-semibold text-gray-700'>$product_name</span>
                    <span class='text-green-600 font-bold'>Updated to ₱" . number_format($new_price, 2) . "</span>
                  </div>";
            $updated_count++;
        } else {
            echo "<div class='flex items-center justify-between p-3 bg-yellow-50 border border-yellow-200 rounded'>
                    <span class='font-semibold text-gray-700'>$product_name</span>
                    <span class='text-yellow-600'>Not found or already updated</span>
                  </div>";
        }
    } else {
        echo "<div class='flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded'>
                <span class='font-semibold text-gray-700'>$product_name</span>
                <span class='text-red-600'>Failed: " . mysqli_error($conn) . "</span>
              </div>";
        $failed_count++;
    }
}

echo "    </div>
        
        <div class='mt-6 p-6 bg-blue-50 border border-blue-200 rounded-lg'>
            <h2 class='text-xl font-bold text-blue-800 mb-3'>Summary</h2>
            <div class='space-y-2 text-gray-700'>
                <p><strong>Total Products:</strong> " . count($price_updates) . "</p>
                <p><strong>Successfully Updated:</strong> <span class='text-green-600 font-bold'>$updated_count</span></p>
                <p><strong>Failed:</strong> <span class='text-red-600 font-bold'>$failed_count</span></p>
            </div>
        </div>
        
        <div class='mt-6 flex gap-4'>
            <a href='customer/menu.php' class='bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600 transition'>
                View Menu
            </a>
            <a href='index.php' class='bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition'>
                Go to Home
            </a>
        </div>
    </div>
</body>
</html>";

mysqli_close($conn);
?>
