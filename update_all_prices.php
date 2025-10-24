<?php
require_once 'db.php';

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Update Prices - Gourmet Sentinel</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h1 { color: #f59e0b; border-bottom: 3px solid #f59e0b; padding-bottom: 10px; }
    .success { color: #059669; margin: 5px 0; padding: 8px; background: #d1fae5; border-radius: 4px; }
    .error { color: #dc2626; margin: 5px 0; padding: 8px; background: #fee2e2; border-radius: 4px; }
    .summary { margin-top: 20px; padding: 20px; background: #e0f2fe; border-radius: 8px; border-left: 4px solid #0284c7; }
    .btn { display: inline-block; background: #f59e0b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; margin-top: 20px; font-weight: bold; }
    .btn:hover { background: #ea580c; }
</style>";
echo "</head><body>";
echo "<div class='container'>";
echo "<h1>📊 Updating Prices to Philippine Market Rates</h1>";

// Philippine market rates based on category
$category_prices = [
    'Burgers' => ['min' => 180, 'max' => 450],
    'Pizza' => ['min' => 280, 'max' => 650],
    'Pasta' => ['min' => 220, 'max' => 420],
    'Salads' => ['min' => 150, 'max' => 280],
    'Desserts' => ['min' => 120, 'max' => 280],
    'Drinks' => ['min' => 80, 'max' => 195]
];

// Specific product pricing
$product_prices = [
    'Caesar Salad' => 180.00,
    'Greek Salad' => 195.00,
    'Garden Salad' => 165.00,
    'Classic Beef Burger' => 285.00,
    'Chicken Burger' => 220.00,
    'Double Cheese Burger' => 350.00,
    'Bacon Burger' => 420.00,
    'Veggie Burger' => 195.00,
    'Margherita Pizza' => 280.00,
    'Pepperoni Pizza' => 350.00,
    'Veggie Pizza' => 295.00,
    'Spaghetti Carbonara' => 250.00,
    'Fettuccine Alfredo' => 295.00,
    'Lasagna' => 320.00,
    'Cheesecake' => 150.00,
    'Chocolate Cake' => 160.00,
    'Tiramisu' => 165.00,
    'Fresh Orange Juice' => 95.00,
    'Mango Smoothie' => 115.00,
    'Iced Coffee' => 125.00,
    'Lemonade' => 85.00,
    'Milkshake' => 135.00
];

$success_count = 0;
$error_count = 0;
$not_found = [];

// Update specific products first
foreach ($product_prices as $product_name => $price) {
    $safe_name = mysqli_real_escape_string($conn, $product_name);
    $query = "UPDATE food_items SET price = $price WHERE name = '$safe_name'";
    
    if (mysqli_query($conn, $query)) {
        $affected = mysqli_affected_rows($conn);
        if ($affected > 0) {
            $success_count++;
            echo "<div class='success'>✓ Updated: <strong>$product_name</strong> → ₱" . number_format($price, 2) . "</div>";
        } else {
            $not_found[] = $product_name;
        }
    } else {
        $error_count++;
        echo "<div class='error'>✗ Error updating $product_name: " . mysqli_error($conn) . "</div>";
    }
}

// Update remaining products by category
$query = "SELECT f.id, f.name, f.price, c.name as category 
          FROM food_items f 
          LEFT JOIN categories c ON f.category_id = c.id";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Skip if already updated
        if (isset($product_prices[$row['name']])) {
            continue;
        }
        
        $category = $row['category'];
        $current_price = floatval($row['price']);
        
        // Determine new price based on category
        $new_price = $current_price;
        
        if (isset($category_prices[$category])) {
            $range = $category_prices[$category];
            
            // If price is too low, set to minimum range
            if ($current_price < $range['min']) {
                $new_price = $range['min'] + rand(0, 50);
            }
            // If price is too high or in USD range (< 50), convert
            elseif ($current_price < 50 || $current_price > $range['max']) {
                // Convert from USD-like pricing to PHP
                if ($current_price < 50) {
                    $new_price = $current_price * 25; // Approximate conversion
                    // Ensure within range
                    $new_price = max($range['min'], min($range['max'], $new_price));
                }
            }
        }
        
        // Round to nearest 5
        $new_price = round($new_price / 5) * 5;
        
        // Update if price changed
        if ($new_price != $current_price) {
            $update_query = "UPDATE food_items SET price = $new_price WHERE id = " . $row['id'];
            if (mysqli_query($conn, $update_query)) {
                $success_count++;
                echo "<div class='success'>✓ Updated: <strong>" . $row['name'] . "</strong> (₱" . number_format($current_price, 2) . " → ₱" . number_format($new_price, 2) . ")</div>";
            }
        }
    }
}

echo "<div class='summary'>";
echo "<h3 style='color: #0284c7; margin: 0 0 15px 0;'>📋 Update Summary</h3>";
echo "<p><strong>✅ Successfully Updated:</strong> $success_count products</p>";
echo "<p><strong>❌ Errors:</strong> $error_count</p>";
if (!empty($not_found)) {
    echo "<p><strong>⚠️ Products Not Found:</strong> " . implode(', ', $not_found) . "</p>";
}
echo "</div>";

echo "<div style='margin-top: 20px;'>";
echo "<a href='check_products.php' class='btn' style='background: #3b82f6;'>📋 View All Products</a> ";
echo "<a href='customer/menu.php' class='btn'>🍴 View Menu</a>";
echo "</div>";

echo "</div>";
echo "</body></html>";
?>
