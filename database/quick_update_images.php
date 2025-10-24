<?php
// Quick Image Update - Run this if the main script doesn't work
require_once '../db.php';

echo "<!DOCTYPE html><html><head><title>Quick Image Update</title></head><body style='font-family: Arial; padding: 20px;'>";
echo "<h1>Quick Image Update</h1>";

// Simple array of updates
$updates = [
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800&q=80' WHERE name = 'Classic Beef Burger'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=800&q=80' WHERE name = 'Chicken Burger'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1520072959219-c595dc870360?w=800&q=80' WHERE name = 'Veggie Burger'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1572802419224-296b0aeee0d9?w=800&q=80' WHERE name = 'Double Cheese Burger'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=800&q=80' WHERE name = 'Spaghetti Carbonara'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=800&q=80' WHERE name = 'Penne Arrabbiata'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1645112411341-6c4fd023714a?w=800&q=80' WHERE name = 'Fettuccine Alfredo'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1574894709920-11b28e7367e3?w=800&q=80' WHERE name = 'Lasagna'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=800&q=80' WHERE name = 'Fresh Orange Juice'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=800&q=80' WHERE name = 'Mango Smoothie'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1517487881594-2787fef5ebf7?w=800&q=80' WHERE name = 'Iced Coffee'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1523677011781-c91d1bbe2f9f?w=800&q=80' WHERE name = 'Lemonade'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=800&q=80' WHERE name = 'Chocolate Cake'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=800&q=80' WHERE name = 'Tiramisu'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=800&q=80' WHERE name = 'Ice Cream Sundae'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1533134486753-c833f0ed4866?w=800&q=80' WHERE name = 'Cheesecake'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=800&q=80' WHERE name = 'Margherita Pizza'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=800&q=80' WHERE name = 'Pepperoni Pizza'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1511689660979-10d2b1aada49?w=800&q=80' WHERE name = 'Vegetarian Pizza'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=800&q=80' WHERE name = 'Caesar Salad'",
    "UPDATE food_items SET image = 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=800&q=80' WHERE name = 'Greek Salad'"
];

$success = 0;
$failed = 0;

foreach ($updates as $sql) {
    if (mysqli_query($conn, $sql)) {
        $success++;
        echo "<p style='color: green;'>✓ Updated successfully</p>";
    } else {
        $failed++;
        echo "<p style='color: red;'>✗ Failed: " . mysqli_error($conn) . "</p>";
    }
}

echo "<h2 style='color: blue;'>Results: $success updated, $failed failed</h2>";
echo "<p><a href='../customer/menu.php' style='background: blue; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Menu</a></p>";
echo "</body></html>";

mysqli_close($conn);
?>
