<?php
// Check current images in database
require_once '../db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check Current Images</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .item { background: white; padding: 15px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .item img { max-width: 200px; height: 150px; object-fit: cover; border-radius: 5px; }
        h1 { color: #333; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Current Food Items & Images</h1>
    
    <?php
    $query = "SELECT id, name, image, category_id FROM food_items ORDER BY category_id, name";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo "<p class='success'>✓ Found " . mysqli_num_rows($result) . " food items</p>";
        
        while ($item = mysqli_fetch_assoc($result)) {
            echo "<div class='item'>";
            echo "<h3>{$item['name']}</h3>";
            echo "<p><strong>Current Image:</strong> " . htmlspecialchars($item['image']) . "</p>";
            
            // Try to display the image
            if (!empty($item['image'])) {
                echo "<img src='{$item['image']}' alt='{$item['name']}' onerror=\"this.style.display='none'; this.nextElementSibling.style.display='block';\">";
                echo "<p style='display:none; color:red;'>⚠️ Image failed to load</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ No image set</p>";
            }
            echo "</div>";
        }
    } else {
        echo "<p class='error'>✗ Error: " . mysqli_error($conn) . "</p>";
    }
    
    mysqli_close($conn);
    ?>
    
    <hr>
    <h2>Quick Actions:</h2>
    <p>
        <a href="quick_update_images.php" style="background: green; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;">Update All Images Now</a>
        <a href="../customer/menu.php" style="background: blue; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;">View Menu</a>
    </p>
</body>
</html>
