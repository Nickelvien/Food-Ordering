<?php
require_once 'db.php';

// Set to true to execute the cleanup, false for preview only
$execute_cleanup = isset($_GET['confirm']) && $_GET['confirm'] == 'yes';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Cleanup - FoodHub</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        h1 { color: #f59e0b; }
        h2 { color: #333; margin-top: 30px; border-bottom: 2px solid #f59e0b; padding-bottom: 10px; }
        .success { color: green; font-weight: bold; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0; }
        .error { color: red; font-weight: bold; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px 0; }
        .warning { color: orange; font-weight: bold; padding: 15px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 5px; margin: 20px 0; }
        .info { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196f3; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; background: white; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th { background: #f59e0b; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f9f9f9; }
        .btn { display: inline-block; padding: 15px 30px; margin: 10px 5px; text-decoration: none; border-radius: 5px; font-weight: bold; cursor: pointer; border: none; font-size: 16px; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        .btn-primary { background: #007bff; color: white; }
        .btn-primary:hover { background: #0056b3; }
        .preview { background: #fff; padding: 20px; border: 2px dashed #f59e0b; margin: 20px 0; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
        .step { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <h1>🛠️ Database Cleanup Tool</h1>
    <p><strong>Action Date:</strong> <?php echo date('F d, Y - h:i:s A'); ?></p>

    <?php
    if (!$execute_cleanup) {
        // PREVIEW MODE
        echo "<div class='warning'>";
        echo "<h2>⚠️ PREVIEW MODE - No Changes Made Yet</h2>";
        echo "<p>This page will show you what will be cleaned up. Click the EXECUTE button below to apply the fixes.</p>";
        echo "</div>";

        // Check what needs to be cleaned
        echo "<div class='step'>";
        echo "<h2>Step 1: Analyzing Database for Duplicates...</h2>";

        // Check 1: Duplicate product names (same name, different IDs)
        $query = "SELECT name, COUNT(*) as count, GROUP_CONCAT(id ORDER BY id) as ids, 
                  GROUP_CONCAT(price ORDER BY id) as prices, MIN(id) as keep_id
                  FROM food_items 
                  GROUP BY name 
                  HAVING count > 1
                  ORDER BY count DESC";
        $result = mysqli_query($conn, $query);
        $dup_names_count = mysqli_num_rows($result);

        if ($dup_names_count > 0) {
            echo "<h3>❌ Found {$dup_names_count} Product Names with Duplicates:</h3>";
            echo "<table>";
            echo "<tr><th>Product Name</th><th>Duplicate Count</th><th>All IDs</th><th>Prices</th><th>Will Keep ID</th><th>Will Delete IDs</th></tr>";
            
            $total_to_delete = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $ids_array = explode(',', $row['ids']);
                $keep_id = $row['keep_id'];
                $delete_ids = array_filter($ids_array, function($id) use ($keep_id) { return $id != $keep_id; });
                $total_to_delete += count($delete_ids);
                
                echo "<tr>";
                echo "<td><strong>{$row['name']}</strong></td>";
                echo "<td>{$row['count']}</td>";
                echo "<td>{$row['ids']}</td>";
                echo "<td>{$row['prices']}</td>";
                echo "<td style='color: green;'>{$keep_id}</td>";
                echo "<td style='color: red;'>" . implode(', ', $delete_ids) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<p><strong>Total duplicate products to delete:</strong> <span style='color: red;'>{$total_to_delete}</span></p>";
        } else {
            echo "<p class='success'>✅ No duplicate product names found!</p>";
        }

        // Check 2: Duplicate category names
        $query = "SELECT name, COUNT(*) as count, GROUP_CONCAT(id ORDER BY id) as ids, MIN(id) as keep_id
                  FROM categories 
                  GROUP BY name 
                  HAVING count > 1";
        $result = mysqli_query($conn, $query);
        $dup_categories_count = mysqli_num_rows($result);

        if ($dup_categories_count > 0) {
            echo "<h3>❌ Found {$dup_categories_count} Category Names with Duplicates:</h3>";
            echo "<table>";
            echo "<tr><th>Category Name</th><th>Duplicate Count</th><th>All IDs</th><th>Will Keep ID</th><th>Will Delete IDs</th></tr>";
            
            while ($row = mysqli_fetch_assoc($result)) {
                $ids_array = explode(',', $row['ids']);
                $keep_id = $row['keep_id'];
                $delete_ids = array_filter($ids_array, function($id) use ($keep_id) { return $id != $keep_id; });
                
                echo "<tr>";
                echo "<td><strong>{$row['name']}</strong></td>";
                echo "<td>{$row['count']}</td>";
                echo "<td>{$row['ids']}</td>";
                echo "<td style='color: green;'>{$keep_id}</td>";
                echo "<td style='color: red;'>" . implode(', ', $delete_ids) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='success'>✅ No duplicate category names found!</p>";
        }

        echo "</div>"; // End step 1

        // Show what will happen
        echo "<div class='step'>";
        echo "<h2>Step 2: Cleanup Actions</h2>";
        echo "<p>When you click <strong>EXECUTE CLEANUP</strong>, the following will happen:</p>";
        echo "<ol>";
        echo "<li>✅ <strong>Keep the FIRST occurrence</strong> of each product name (lowest ID)</li>";
        echo "<li>❌ <strong>Delete all DUPLICATE occurrences</strong> (higher IDs)</li>";
        echo "<li>✅ <strong>Keep the FIRST occurrence</strong> of each category name</li>";
        echo "<li>❌ <strong>Delete duplicate categories</strong> and update product references</li>";
        echo "<li>✅ <strong>Optimize database tables</strong> for better performance</li>";
        echo "</ol>";
        echo "</div>";

        // Show current stats
        echo "<div class='step'>";
        echo "<h2>Step 3: Current Database Statistics</h2>";
        
        $total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM food_items"))['count'];
        $active_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM food_items WHERE is_available = 1"))['count'];
        $total_categories = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM categories"))['count'];
        
        echo "<table>";
        echo "<tr><th>Metric</th><th>Before Cleanup</th><th>After Cleanup (Estimated)</th></tr>";
        echo "<tr><td>Total Products</td><td>{$total_products}</td><td>" . ($total_products - $total_to_delete) . "</td></tr>";
        echo "<tr><td>Active Products (Menu)</td><td>{$active_products}</td><td>~" . ($active_products - $total_to_delete) . "</td></tr>";
        echo "<tr><td>Total Categories</td><td>{$total_categories}</td><td>{$total_categories}</td></tr>";
        echo "</table>";
        echo "</div>";

        // Buttons
        echo "<div class='step' style='text-align: center;'>";
        echo "<h2>Ready to Clean Database?</h2>";
        if ($dup_names_count > 0 || $dup_categories_count > 0) {
            echo "<p class='warning'>⚠️ <strong>WARNING:</strong> This will permanently delete {$total_to_delete} duplicate products from the database!</p>";
            echo "<p>This action CANNOT be undone. Make sure you have a backup!</p>";
            echo "<a href='fix_database_duplicates.php?confirm=yes' class='btn btn-danger' onclick='return confirm(\"Are you SURE you want to delete {$total_to_delete} duplicate products? This cannot be undone!\");'>🗑️ EXECUTE CLEANUP NOW</a>";
        } else {
            echo "<p class='success'>✅ Your database is already clean! No duplicates found.</p>";
        }
        echo "<a href='check_database_duplicates.php' class='btn btn-primary'>📊 View Detailed Report</a>";
        echo "<a href='customer/menu.php' class='btn btn-success'>🍽️ Go to Menu</a>";
        echo "</div>";

    } else {
        // EXECUTE MODE
        echo "<div class='error'>";
        echo "<h2>🔧 EXECUTING DATABASE CLEANUP...</h2>";
        echo "</div>";

        $cleanup_log = [];
        $errors = [];

        // Step 1: Delete duplicate products (keep lowest ID)
        echo "<div class='step'>";
        echo "<h2>Step 1: Removing Duplicate Products...</h2>";
        
        $query = "SELECT name, MIN(id) as keep_id, GROUP_CONCAT(id ORDER BY id) as all_ids
                  FROM food_items 
                  GROUP BY name 
                  HAVING COUNT(*) > 1";
        $result = mysqli_query($conn, $query);
        
        $deleted_count = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['name'];
            $keep_id = $row['keep_id'];
            $all_ids = explode(',', $row['all_ids']);
            
            foreach ($all_ids as $id) {
                if ($id != $keep_id) {
                    $delete_query = "DELETE FROM food_items WHERE id = $id";
                    if (mysqli_query($conn, $delete_query)) {
                        $deleted_count++;
                        $cleanup_log[] = "✅ Deleted duplicate product: {$name} (ID: {$id}, kept ID: {$keep_id})";
                    } else {
                        $errors[] = "❌ Failed to delete product ID {$id}: " . mysqli_error($conn);
                    }
                }
            }
        }
        
        if ($deleted_count > 0) {
            echo "<p class='success'>✅ Deleted {$deleted_count} duplicate products</p>";
        } else {
            echo "<p class='success'>✅ No duplicate products to delete</p>";
        }
        echo "</div>";

        // Step 2: Delete duplicate categories
        echo "<div class='step'>";
        echo "<h2>Step 2: Removing Duplicate Categories...</h2>";
        
        $query = "SELECT name, MIN(id) as keep_id, GROUP_CONCAT(id ORDER BY id) as all_ids
                  FROM categories 
                  GROUP BY name 
                  HAVING COUNT(*) > 1";
        $result = mysqli_query($conn, $query);
        
        $deleted_categories = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['name'];
            $keep_id = $row['keep_id'];
            $all_ids = explode(',', $row['all_ids']);
            
            foreach ($all_ids as $id) {
                if ($id != $keep_id) {
                    // Update products to use the kept category ID
                    $update_query = "UPDATE food_items SET category_id = {$keep_id} WHERE category_id = {$id}";
                    mysqli_query($conn, $update_query);
                    
                    // Delete the duplicate category
                    $delete_query = "DELETE FROM categories WHERE id = {$id}";
                    if (mysqli_query($conn, $delete_query)) {
                        $deleted_categories++;
                        $cleanup_log[] = "✅ Deleted duplicate category: {$name} (ID: {$id}, kept ID: {$keep_id})";
                    } else {
                        $errors[] = "❌ Failed to delete category ID {$id}: " . mysqli_error($conn);
                    }
                }
            }
        }
        
        if ($deleted_categories > 0) {
            echo "<p class='success'>✅ Deleted {$deleted_categories} duplicate categories</p>";
        } else {
            echo "<p class='success'>✅ No duplicate categories to delete</p>";
        }
        echo "</div>";

        // Step 3: Optimize tables
        echo "<div class='step'>";
        echo "<h2>Step 3: Optimizing Database Tables...</h2>";
        
        mysqli_query($conn, "OPTIMIZE TABLE food_items");
        mysqli_query($conn, "OPTIMIZE TABLE categories");
        
        echo "<p class='success'>✅ Database tables optimized</p>";
        echo "</div>";

        // Show cleanup log
        echo "<div class='step'>";
        echo "<h2>📋 Cleanup Log</h2>";
        if (count($cleanup_log) > 0) {
            echo "<ul style='list-style: none; padding: 0;'>";
            foreach ($cleanup_log as $log) {
                echo "<li>$log</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No changes were needed.</p>";
        }
        echo "</div>";

        // Show errors if any
        if (count($errors) > 0) {
            echo "<div class='error'>";
            echo "<h2>❌ Errors Encountered</h2>";
            echo "<ul>";
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
            echo "</div>";
        }

        // Final statistics
        echo "<div class='step'>";
        echo "<h2>✅ Cleanup Complete!</h2>";
        
        $total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM food_items"))['count'];
        $active_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM food_items WHERE is_available = 1"))['count'];
        $unique_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT name) as count FROM food_items"))['count'];
        
        echo "<table>";
        echo "<tr><th>Metric</th><th>Value</th></tr>";
        echo "<tr><td>Total Products in Database</td><td>{$total_products}</td></tr>";
        echo "<tr><td>Active Products (Menu)</td><td>{$active_products}</td></tr>";
        echo "<tr><td>Unique Product Names</td><td>{$unique_products}</td></tr>";
        echo "<tr><td>Products Deleted</td><td style='color: red;'>{$deleted_count}</td></tr>";
        echo "<tr><td>Categories Cleaned</td><td style='color: orange;'>{$deleted_categories}</td></tr>";
        echo "</table>";
        
        echo "<p class='success' style='font-size: 18px;'>🎉 Your database is now clean and optimized!</p>";
        echo "</div>";

        // Action buttons
        echo "<div class='step' style='text-align: center;'>";
        echo "<h2>What's Next?</h2>";
        echo "<p>Your database has been cleaned. Now test your menu page!</p>";
        echo "<a href='customer/menu.php' class='btn btn-success'>🍽️ View Clean Menu</a>";
        echo "<a href='check_database_duplicates.php' class='btn btn-primary'>📊 Run Diagnostic Again</a>";
        echo "</div>";
    }
    ?>

</body>
</html>
