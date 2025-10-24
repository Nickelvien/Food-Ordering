<?php
require_once 'db.php';

echo "<h1>Database Duplicate Check</h1>";

// Check for duplicate category IDs
echo "<h2>1. Checking for Duplicate Category IDs:</h2>";
$query = "SELECT id, name, COUNT(*) as count 
          FROM categories 
          GROUP BY id 
          HAVING count > 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    echo "<p style='color: red;'>❌ FOUND DUPLICATE IDs:</p>";
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Count</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['count']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: green;'>✅ No duplicate IDs found (Good!)</p>";
}

// Check for duplicate category names
echo "<h2>2. Checking for Duplicate Category Names:</h2>";
$query = "SELECT name, COUNT(*) as count 
          FROM categories 
          GROUP BY name 
          HAVING count > 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    echo "<p style='color: orange;'>⚠️ FOUND DUPLICATE NAMES:</p>";
    echo "<table border='1'><tr><th>Category Name</th><th>Count</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['name']}</td><td>{$row['count']}</td></tr>";
    }
    echo "</table>";
    echo "<p><em>Note: Different IDs with same name is okay, your code handles this correctly.</em></p>";
} else {
    echo "<p style='color: green;'>✅ No duplicate names found</p>";
}

// Show all active categories
echo "<h2>3. All Active Categories:</h2>";
$query = "SELECT id, name, is_active FROM categories WHERE is_active = 1 ORDER BY name";
$result = mysqli_query($conn, $query);

echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Active</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['is_active']}</td></tr>";
}
echo "</table>";

// Test the unique array logic
echo "<h2>4. Testing Unique Array Logic:</h2>";
$categories_query = "SELECT DISTINCT id, name FROM categories WHERE is_active = 1 ORDER BY name";
$categories_result = mysqli_query($conn, $categories_query);

$unique_categories = [];
if ($categories_result && mysqli_num_rows($categories_result) > 0) {
    while ($cat = mysqli_fetch_assoc($categories_result)) {
        $unique_categories[$cat['id']] = $cat;
    }
}

echo "<p>Database returned: " . mysqli_num_rows($categories_result) . " rows</p>";
echo "<p>Unique array contains: " . count($unique_categories) . " categories</p>";

echo "<table border='1'><tr><th>Array Key (ID)</th><th>Category Name</th></tr>";
foreach ($unique_categories as $id => $category) {
    echo "<tr><td>{$id}</td><td>{$category['name']}</td></tr>";
}
echo "</table>";

echo "<h2>✅ Diagnostic Complete!</h2>";
echo "<p><a href='customer/menu.php'>Back to Menu</a></p>";
?>
