<?php
require_once 'db.php';

// Update all product prices to Philippine market rates
$updates = [
    // BURGERS (₱250-₱450)
    "UPDATE food_items SET price = 285.00 WHERE name = 'Classic Beef Burger'",
    "UPDATE food_items SET price = 250.00 WHERE name = 'Chicken Burger'",
    "UPDATE food_items SET price = 350.00 WHERE name = 'Double Cheese Burger'",
    "UPDATE food_items SET price = 420.00 WHERE name = 'Bacon Burger'",
    "UPDATE food_items SET price = 195.00 WHERE name = 'Veggie Burger'",
    
    // PIZZA (₱350-₱650)
    "UPDATE food_items SET price = 420.00 WHERE name = 'Margherita Pizza'",
    "UPDATE food_items SET price = 495.00 WHERE name = 'Pepperoni Pizza'",
    "UPDATE food_items SET price = 385.00 WHERE name = 'Veggie Pizza'",
    "UPDATE food_items SET price = 575.00 WHERE name = 'BBQ Chicken Pizza'",
    "UPDATE food_items SET price = 625.00 WHERE name = 'Four Cheese Pizza'",
    
    // PASTA (₱280-₱420)
    "UPDATE food_items SET price = 320.00 WHERE name = 'Spaghetti Carbonara'",
    "UPDATE food_items SET price = 295.00 WHERE name = 'Fettuccine Alfredo'",
    "UPDATE food_items SET price = 385.00 WHERE name = 'Lasagna'",
    "UPDATE food_items SET price = 340.00 WHERE name = 'Penne Arrabbiata'",
    "UPDATE food_items SET price = 410.00 WHERE name = 'Seafood Pasta'",
    
    // SALADS (₱180-₱280)
    "UPDATE food_items SET price = 220.00 WHERE name = 'Caesar Salad'",
    "UPDATE food_items SET price = 235.00 WHERE name = 'Greek Salad'",
    "UPDATE food_items SET price = 195.00 WHERE name = 'Garden Salad'",
    "UPDATE food_items SET price = 265.00 WHERE name = 'Chicken Caesar Salad'",
    "UPDATE food_items SET price = 185.00 WHERE name = 'House Salad'",
    
    // DESSERTS (₱150-₱280)
    "UPDATE food_items SET price = 185.00 WHERE name = 'Cheesecake'",
    "UPDATE food_items SET price = 195.00 WHERE name = 'Chocolate Cake'",
    "UPDATE food_items SET price = 165.00 WHERE name = 'Tiramisu'",
    "UPDATE food_items SET price = 145.00 WHERE name = 'Ice Cream Sundae'",
    "UPDATE food_items SET price = 210.00 WHERE name = 'Brownie with Ice Cream'",
    
    // DRINKS (₱85-₱195)
    "UPDATE food_items SET price = 95.00 WHERE name = 'Fresh Orange Juice'",
    "UPDATE food_items SET price = 115.00 WHERE name = 'Mango Smoothie'",
    "UPDATE food_items SET price = 125.00 WHERE name = 'Iced Coffee'",
    "UPDATE food_items SET price = 85.00 WHERE name = 'Lemonade'",
    "UPDATE food_items SET price = 105.00 WHERE name = 'Green Tea'",
    "UPDATE food_items SET price = 135.00 WHERE name = 'Milkshake'"
];

$success_count = 0;
$error_count = 0;

echo "<h2>Updating Product Prices to Philippine Market Rates...</h2>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 8px;'>";

foreach ($updates as $query) {
    if (mysqli_query($conn, $query)) {
        $affected = mysqli_affected_rows($conn);
        if ($affected > 0) {
            $success_count++;
            preg_match("/name = '([^']+)'/", $query, $matches);
            preg_match("/price = ([\d.]+)/", $query, $price_matches);
            $product = $matches[1] ?? 'Unknown';
            $price = $price_matches[1] ?? '0.00';
            echo "<div style='color: green; margin: 5px 0;'>✓ Updated: <strong>$product</strong> → ₱$price</div>";
        }
    } else {
        $error_count++;
        echo "<div style='color: red; margin: 5px 0;'>✗ Error: " . mysqli_error($conn) . "</div>";
    }
}

echo "</div>";
echo "<div style='margin-top: 20px; padding: 15px; background: #e3f2fd; border-radius: 8px;'>";
echo "<h3 style='color: #1976d2; margin: 0 0 10px 0;'>Summary</h3>";
echo "<p style='margin: 5px 0;'><strong>Successfully Updated:</strong> $success_count products</p>";
echo "<p style='margin: 5px 0;'><strong>Errors:</strong> $error_count</p>";
echo "</div>";

echo "<div style='margin-top: 20px;'>";
echo "<a href='customer/menu.php' style='display: inline-block; background: #f59e0b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold;'>View Updated Menu</a>";
echo "</div>";
?>
