<?php
require_once 'db.php';

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Product List - Gourmet Sentinel</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h1 { color: #f59e0b; border-bottom: 3px solid #f59e0b; padding-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th { background: #f59e0b; color: white; padding: 12px; text-align: left; }
    td { padding: 10px; border-bottom: 1px solid #ddd; }
    tr:hover { background: #fff7ed; }
    .price { color: #059669; font-weight: bold; }
    .old-price { color: #dc2626; text-decoration: line-through; }
    .category { background: #fef3c7; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
    .btn { display: inline-block; background: #f59e0b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; margin-top: 20px; font-weight: bold; }
    .btn:hover { background: #ea580c; }
</style>";
echo "</head><body>";
echo "<div class='container'>";
echo "<h1>🍽️ Current Products in Database</h1>";

// Get all products with category names
$query = "SELECT f.id, f.name, f.price, f.description, c.name as category_name 
          FROM food_items f 
          LEFT JOIN categories c ON f.category_id = c.id 
          ORDER BY c.name, f.name";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<p>Total Products: <strong>" . mysqli_num_rows($result) . "</strong></p>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Product Name</th><th>Category</th><th>Current Price</th><th>Description</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><strong>" . htmlspecialchars($row['name']) . "</strong></td>";
        echo "<td><span class='category'>" . htmlspecialchars($row['category_name'] ?? 'N/A') . "</span></td>";
        echo "<td class='price'>₱" . number_format($row['price'], 2) . "</td>";
        echo "<td>" . htmlspecialchars(substr($row['description'] ?? '', 0, 50)) . "...</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "<br><a href='update_all_prices.php' class='btn'>📊 Update All Prices to Philippine Rates</a>";
    echo " <a href='customer/menu.php' class='btn' style='background: #3b82f6;'>🍴 View Menu</a>";
} else {
    echo "<p style='color: red;'>No products found in database!</p>";
}

echo "</div>";
echo "</body></html>";
?>
