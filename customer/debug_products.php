<?php
session_start();
require_once '../db.php';

echo "<h1>Product Database Debug</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #f59e0b; color: white; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .duplicate { background-color: #ffcccc !important; font-weight: bold; }
    .info { background-color: #e3f2fd; padding: 15px; margin: 20px 0; border-left: 4px solid #2196F3; }
</style>";

// Check for duplicate product IDs
$check_duplicates = "SELECT id, name, COUNT(*) as count 
                     FROM food_items 
                     GROUP BY id 
                     HAVING count > 1";
$dup_result = mysqli_query($conn, $check_duplicates);

if (mysqli_num_rows($dup_result) > 0) {
    echo "<div class='info' style='background-color: #ffebee; border-color: #f44336;'>";
    echo "<h2>⚠️ DUPLICATE IDs FOUND IN DATABASE!</h2>";
    echo "<p>These product IDs appear more than once:</p>";
    echo "<ul>";
    while ($dup = mysqli_fetch_assoc($dup_result)) {
        echo "<li>ID: {$dup['id']} - {$dup['name']} appears {$dup['count']} times</li>";
    }
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div class='info'>";
    echo "<h2>✅ No duplicate IDs found</h2>";
    echo "<p>Each product has a unique ID in the database.</p>";
    echo "</div>";
}

// Show all products
echo "<h2>All Products in Database</h2>";
$query = "SELECT f.*, c.name as category_name 
          FROM food_items f 
          LEFT JOIN categories c ON f.category_id = c.id 
          ORDER BY f.id";
$result = mysqli_query($conn, $query);

echo "<table>";
echo "<tr>
        <th>ID</th>
        <th>Name</th>
        <th>Category</th>
        <th>Price</th>
        <th>Available</th>
        <th>Image</th>
      </tr>";

$seen_ids = [];
while ($row = mysqli_fetch_assoc($result)) {
    $is_duplicate = isset($seen_ids[$row['id']]) ? "duplicate" : "";
    $seen_ids[$row['id']] = true;
    
    echo "<tr class='{$is_duplicate}'>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['name']}</td>";
    echo "<td>{$row['category_name']}</td>";
    echo "<td>₱" . number_format($row['price'], 2) . "</td>";
    echo "<td>" . ($row['is_available'] ? 'Yes' : 'No') . "</td>";
    echo "<td>" . substr($row['image'], 0, 50) . "...</td>";
    echo "</tr>";
}
echo "</table>";

// Show total counts
$total = "SELECT COUNT(*) as total FROM food_items WHERE is_available = 1";
$total_result = mysqli_query($conn, $total);
$total_row = mysqli_fetch_assoc($total_result);

$unique = "SELECT COUNT(DISTINCT id) as unique_count FROM food_items WHERE is_available = 1";
$unique_result = mysqli_query($conn, $unique);
$unique_row = mysqli_fetch_assoc($unique_result);

echo "<div class='info'>";
echo "<h3>Statistics</h3>";
echo "<p><strong>Total available products:</strong> {$total_row['total']}</p>";
echo "<p><strong>Unique product IDs:</strong> {$unique_row['unique_count']}</p>";
if ($total_row['total'] > $unique_row['unique_count']) {
    echo "<p style='color: red;'><strong>⚠️ There are duplicate rows in your database!</strong></p>";
    echo "<p>You need to clean up your database to remove duplicate entries.</p>";
} else {
    echo "<p style='color: green;'><strong>✅ No duplicate rows found!</strong></p>";
}
echo "</div>";

// Test the menu query
echo "<h2>Test Menu Query Results</h2>";
$menu_query = "SELECT f.id, f.name, f.description, f.price, f.image, f.category_id, c.name as category_name 
               FROM food_items f 
               INNER JOIN categories c ON f.category_id = c.id 
               WHERE f.is_available = 1
               ORDER BY f.name ASC";
$menu_result = mysqli_query($conn, $menu_query);

echo "<p><strong>Query returned:</strong> " . mysqli_num_rows($menu_result) . " rows</p>";

echo "<table>";
echo "<tr><th>Row #</th><th>ID</th><th>Name</th><th>Category</th></tr>";
$row_num = 1;
$menu_seen = [];
while ($row = mysqli_fetch_assoc($menu_result)) {
    $is_dup = isset($menu_seen[$row['id']]) ? "duplicate" : "";
    $menu_seen[$row['id']] = true;
    
    echo "<tr class='{$is_dup}'>";
    echo "<td>{$row_num}</td>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['name']}</td>";
    echo "<td>{$row['category_name']}</td>";
    echo "</tr>";
    $row_num++;
}
echo "</table>";

echo "<div class='info'>";
echo "<h3>Solution Applied</h3>";
echo "<p>The menu.php page now uses a <strong>unique items array</strong> that automatically filters out duplicates by product ID.</p>";
echo "<p>Even if the database returns duplicate rows, each product will only appear once on the menu page.</p>";
echo "</div>";
?>

<div style="margin-top: 30px; padding: 20px; background: #fff3cd; border-left: 4px solid #ffc107;">
    <h3>🔧 How to Clean Database (if needed)</h3>
    <p>If you see duplicate rows above, run this SQL to keep only unique products:</p>
    <pre style="background: #f5f5f5; padding: 10px; overflow-x: auto;">
-- This will keep the first occurrence of each product and delete duplicates
DELETE t1 FROM food_items t1
INNER JOIN food_items t2 
WHERE t1.id = t2.id AND t1.created_at > t2.created_at;
</pre>
    <p><strong>⚠️ BACKUP your database before running this!</strong></p>
</div>

<div style="margin-top: 20px;">
    <a href="menu.php" style="display: inline-block; padding: 10px 20px; background: #f59e0b; color: white; text-decoration: none; border-radius: 5px;">← Back to Menu</a>
</div>
