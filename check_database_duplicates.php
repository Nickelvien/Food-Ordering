<?php
require_once 'db.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Duplicate Check - FoodHub</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        h1 { color: #f59e0b; }
        h2 { color: #333; margin-top: 30px; border-bottom: 2px solid #f59e0b; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; background: white; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th { background: #f59e0b; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f9f9f9; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .info { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196f3; margin: 20px 0; }
        .stats { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .back-link { display: inline-block; margin: 20px 0; padding: 10px 20px; background: #f59e0b; color: white; text-decoration: none; border-radius: 5px; }
        .back-link:hover { background: #fb923c; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 Database Duplicate Analysis</h1>
    <p><strong>Database Check Date:</strong> <?php echo date('F d, Y - h:i:s A'); ?></p>

    <?php
    // CHECK 1: Duplicate Product IDs
    echo "<h2>1. Checking for Duplicate Product IDs (Primary Key)</h2>";
    $query = "SELECT id, name, price, category_id, COUNT(*) as count 
              FROM food_items 
              GROUP BY id 
              HAVING count > 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "<p class='error'>❌ CRITICAL: Found duplicate Product IDs (This should NEVER happen!):</p>";
        echo "<table><tr><th>Product ID</th><th>Name</th><th>Price</th><th>Category ID</th><th>Duplicate Count</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>₱{$row['price']}</td><td>{$row['category_id']}</td><td class='error'>{$row['count']}</td></tr>";
        }
        echo "</table>";
        echo "<div class='info'><strong>🛠️ Fix:</strong> Your database has corrupted primary keys. You need to run a cleanup query to remove duplicate IDs.</div>";
    } else {
        echo "<p class='success'>✅ No duplicate Product IDs found (Good! Primary key is working correctly)</p>";
    }

    // CHECK 2: Duplicate Product Names (different IDs)
    echo "<h2>2. Checking for Products with Same Name (Different IDs)</h2>";
    $query = "SELECT name, COUNT(*) as count, GROUP_CONCAT(id ORDER BY id) as product_ids, GROUP_CONCAT(price ORDER BY id) as prices
              FROM food_items 
              GROUP BY name 
              HAVING count > 1
              ORDER BY count DESC";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "<p class='warning'>⚠️ Found products with same name but different IDs:</p>";
        echo "<table><tr><th>Product Name</th><th>Count</th><th>Product IDs</th><th>Prices</th><th>Status</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            $ids_array = explode(',', $row['product_ids']);
            $prices_array = explode(',', $row['prices']);
            $status = (count(array_unique($prices_array)) > 1) ? "Different prices (might be variants)" : "Same price (likely duplicates)";
            $status_class = (count(array_unique($prices_array)) > 1) ? "warning" : "error";
            echo "<tr><td>{$row['name']}</td><td class='error'>{$row['count']}</td><td>{$row['product_ids']}</td><td>{$row['prices']}</td><td class='$status_class'>{$status}</td></tr>";
        }
        echo "</table>";
        echo "<div class='info'><strong>📝 Note:</strong> These are DIFFERENT products with the SAME name. They will ALL appear on your menu (which may be intentional if they're different sizes/variants).</div>";
    } else {
        echo "<p class='success'>✅ No products with duplicate names found</p>";
    }

    // CHECK 3: All Products in Database
    echo "<h2>3. All Products in Database (Active & Inactive)</h2>";
    $query = "SELECT f.id, f.name, f.price, f.is_available, c.name as category_name, 
              (SELECT COUNT(*) FROM food_items f2 WHERE f2.name = f.name) as name_count
              FROM food_items f 
              LEFT JOIN categories c ON f.category_id = c.id
              ORDER BY f.name, f.id";
    $result = mysqli_query($conn, $query);

    $total_products = mysqli_num_rows($result);
    echo "<div class='stats'><strong>Total Products in Database:</strong> $total_products</div>";
    
    echo "<table><tr><th>ID</th><th>Product Name</th><th>Price</th><th>Category</th><th>Available</th><th>Duplicate Name Count</th></tr>";
    $active_count = 0;
    $inactive_count = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $available = $row['is_available'] ? 'Yes' : 'No';
        $available_class = $row['is_available'] ? 'success' : 'error';
        $duplicate_class = $row['name_count'] > 1 ? 'error' : 'success';
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>₱" . number_format($row['price'], 2) . "</td>
            <td>{$row['category_name']}</td>
            <td class='$available_class'>{$available}</td>
            <td class='$duplicate_class'>{$row['name_count']}</td>
        </tr>";
        
        if ($row['is_available']) $active_count++;
        else $inactive_count++;
    }
    echo "</table>";
    echo "<div class='stats'>";
    echo "<strong>Active Products (shown on menu):</strong> $active_count<br>";
    echo "<strong>Inactive Products (hidden from menu):</strong> $inactive_count";
    echo "</div>";

    // CHECK 4: Duplicate Categories
    echo "<h2>4. Checking Category Duplicates</h2>";
    $query = "SELECT id, name, is_active, COUNT(*) as count 
              FROM categories 
              GROUP BY id 
              HAVING count > 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "<p class='error'>❌ Found duplicate Category IDs:</p>";
        echo "<table><tr><th>Category ID</th><th>Name</th><th>Active</th><th>Count</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['is_active']}</td><td class='error'>{$row['count']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='success'>✅ No duplicate Category IDs found</p>";
    }

    // CHECK 5: Category Names (duplicate names, different IDs)
    echo "<h2>5. Categories with Same Name (Different IDs)</h2>";
    $query = "SELECT name, COUNT(*) as count, GROUP_CONCAT(id ORDER BY id) as category_ids
              FROM categories 
              GROUP BY name 
              HAVING count > 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "<p class='warning'>⚠️ Found categories with same name but different IDs:</p>";
        echo "<table><tr><th>Category Name</th><th>Count</th><th>Category IDs</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr><td>{$row['name']}</td><td class='error'>{$row['count']}</td><td>{$row['category_ids']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='success'>✅ No categories with duplicate names</p>";
    }

    // CHECK 6: Simulate the menu query
    echo "<h2>6. Testing Current Menu Query (What menu.php shows)</h2>";
    $query = "SELECT f.id, f.name, f.description, f.price, f.image, f.category_id, c.name as category_name 
              FROM food_items f 
              INNER JOIN categories c ON f.category_id = c.id 
              WHERE f.is_available = 1
              GROUP BY f.id
              ORDER BY f.name ASC";
    $result = mysqli_query($conn, $query);
    
    $menu_count = mysqli_num_rows($result);
    echo "<div class='stats'><strong>Products Shown on Menu (with GROUP BY fix):</strong> $menu_count items</div>";
    
    echo "<table><tr><th>ID</th><th>Product Name</th><th>Category</th><th>Price</th><th>Description</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['category_name']}</td>
            <td>₱" . number_format($row['price'], 2) . "</td>
            <td>" . substr($row['description'], 0, 50) . "...</td>
        </tr>";
    }
    echo "</table>";

    // CHECK 7: Query WITHOUT GROUP BY (showing the problem)
    echo "<h2>7. Testing WITHOUT GROUP BY (The Problem You Were Having)</h2>";
    $query = "SELECT f.id, f.name, f.description, f.price, f.image, f.category_id, c.name as category_name 
              FROM food_items f 
              INNER JOIN categories c ON f.category_id = c.id 
              WHERE f.is_available = 1
              ORDER BY f.name ASC";
    $result = mysqli_query($conn, $query);
    
    $no_group_count = mysqli_num_rows($result);
    echo "<div class='stats'>";
    echo "<strong>Products WITHOUT GROUP BY:</strong> $no_group_count items<br>";
    if ($no_group_count > $menu_count) {
        echo "<span class='error'>⚠️ This shows " . ($no_group_count - $menu_count) . " extra duplicate rows!</span>";
    } else {
        echo "<span class='success'>✅ No duplicates detected in query</span>";
    }
    echo "</div>";

    // Show first 20 results
    echo "<p><em>Showing first 20 results (look for duplicate product names):</em></p>";
    echo "<table><tr><th>Row #</th><th>ID</th><th>Product Name</th><th>Category</th><th>Price</th></tr>";
    $row_num = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row_num > 20) break;
        echo "<tr>
            <td>$row_num</td>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['category_name']}</td>
            <td>₱" . number_format($row['price'], 2) . "</td>
        </tr>";
        $row_num++;
    }
    echo "</table>";

    // SUMMARY AND RECOMMENDATIONS
    echo "<h2>📊 Summary & Recommendations</h2>";
    echo "<div class='stats'>";
    
    // Recount for summary
    $dup_ids_query = "SELECT COUNT(*) as count FROM (SELECT id FROM food_items GROUP BY id HAVING COUNT(*) > 1) as dups";
    $dup_ids_result = mysqli_query($conn, $dup_ids_query);
    $dup_ids_count = mysqli_fetch_assoc($dup_ids_result)['count'];
    
    $dup_names_query = "SELECT COUNT(*) as count FROM (SELECT name FROM food_items GROUP BY name HAVING COUNT(*) > 1) as dups";
    $dup_names_result = mysqli_query($conn, $dup_names_query);
    $dup_names_count = mysqli_fetch_assoc($dup_names_result)['count'];
    
    echo "<strong>Diagnostic Results:</strong><br><br>";
    
    if ($dup_ids_count > 0) {
        echo "❌ <strong class='error'>CRITICAL:</strong> {$dup_ids_count} product(s) have duplicate IDs<br>";
        echo "   <strong>Action:</strong> Run database cleanup to remove duplicate IDs<br><br>";
    } else {
        echo "✅ <strong class='success'>Product IDs are unique</strong> (Primary key working correctly)<br><br>";
    }
    
    if ($dup_names_count > 0) {
        echo "⚠️ <strong class='warning'>WARNING:</strong> {$dup_names_count} product name(s) appear multiple times with different IDs<br>";
        echo "   <strong>Action:</strong> Review if these are intentional variants or accidental duplicates<br><br>";
    } else {
        echo "✅ <strong class='success'>All product names are unique</strong><br><br>";
    }
    
    if ($no_group_count > $menu_count) {
        echo "✅ <strong class='success'>GROUP BY fix is working!</strong> Reduced from {$no_group_count} to {$menu_count} items<br>";
        echo "   <strong>Note:</strong> The GROUP BY in menu.php is preventing duplicates from showing<br><br>";
    } else {
        echo "✅ <strong class='success'>No duplicates detected in database queries</strong><br><br>";
    }
    
    echo "<strong>Current Menu Status:</strong><br>";
    echo "• Shows <strong>{$menu_count} unique products</strong> (with GROUP BY)<br>";
    echo "• Database contains <strong>{$total_products} total products</strong> ({$active_count} active, {$inactive_count} inactive)<br>";
    
    echo "</div>";

    // Provide SQL cleanup queries if needed
    if ($dup_names_count > 0) {
        echo "<h2>🛠️ Cleanup Queries (Use with Caution)</h2>";
        echo "<div class='info'>";
        echo "<p><strong>⚠️ BACKUP YOUR DATABASE FIRST!</strong></p>";
        echo "<p>To find and review duplicate product names, run this in phpMyAdmin:</p>";
        echo "<code>SELECT name, GROUP_CONCAT(id) as ids, COUNT(*) as count FROM food_items GROUP BY name HAVING count > 1;</code><br><br>";
        echo "<p>To keep only the FIRST occurrence of each product name (deletes others):</p>";
        echo "<code>DELETE t1 FROM food_items t1<br>INNER JOIN food_items t2<br>WHERE t1.id > t2.id AND t1.name = t2.name;</code>";
        echo "</div>";
    }
    ?>

    <a href="customer/menu.php" class="back-link">← Back to Menu</a>
    <a href="check_database_duplicates.php" class="back-link" style="background: #2196f3;">🔄 Refresh Check</a>

</body>
</html>
