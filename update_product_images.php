<?php
require_once 'db.php';

// Unsplash image mappings (high-quality, free to use)
// These are direct links to high-quality food images
$product_images = [
    // Burgers
    'Classic Burger' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800&h=600&fit=crop',
    'Cheese Burger' => 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=800&h=600&fit=crop',
    'Bacon Burger' => 'https://images.unsplash.com/photo-1553979459-d2229ba7433b?w=800&h=600&fit=crop',
    'Double Burger' => 'https://images.unsplash.com/photo-1572802419224-296b0aeee0d9?w=800&h=600&fit=crop',
    'Veggie Burger' => 'https://images.unsplash.com/photo-1520072959219-c595dc870360?w=800&h=600&fit=crop',
    
    // Desserts
    'Cheesecake' => 'https://images.unsplash.com/photo-1533134486753-c833f0ed4866?w=800&h=600&fit=crop',
    'Chocolate Cake' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=800&h=600&fit=crop',
    'Ice Cream' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=800&h=600&fit=crop',
    'Tiramisu' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=800&h=600&fit=crop',
    'Brownie' => 'https://images.unsplash.com/photo-1607920591413-4ec007e70023?w=800&h=600&fit=crop',
    
    // Drinks
    'Orange Juice' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=800&h=600&fit=crop',
    'Coca Cola' => 'https://images.unsplash.com/photo-1554866585-cd94860890b7?w=800&h=600&fit=crop',
    'Iced Coffee' => 'https://images.unsplash.com/photo-1517487881594-2787fef5ebf7?w=800&h=600&fit=crop',
    'Lemonade' => 'https://images.unsplash.com/photo-1523677011781-c91d1bbe2f0d?w=800&h=600&fit=crop',
    'Smoothie' => 'https://images.unsplash.com/photo-1505252585461-04db1eb84625?w=800&h=600&fit=crop',
    
    // Pasta
    'Spaghetti Carbonara' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=800&h=600&fit=crop',
    'Pasta Alfredo' => 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=800&h=600&fit=crop',
    'Penne Arrabiata' => 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=800&h=600&fit=crop',
    'Lasagna' => 'https://images.unsplash.com/photo-1574894709920-11b28e7367e3?w=800&h=600&fit=crop',
    
    // Pizza
    'Margherita Pizza' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=800&h=600&fit=crop',
    'Pepperoni Pizza' => 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=800&h=600&fit=crop',
    'Hawaiian Pizza' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=800&h=600&fit=crop',
    'Veggie Pizza' => 'https://images.unsplash.com/photo-1511689660979-10d2b1aada49?w=800&h=600&fit=crop',
    
    // Salads
    'Caesar Salad' => 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=800&h=600&fit=crop',
    'Greek Salad' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=800&h=600&fit=crop',
    'Garden Salad' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&h=600&fit=crop',
    'Fruit Salad' => 'https://images.unsplash.com/photo-1564093497595-593b96d80180?w=800&h=600&fit=crop',
];

// Alternative: Pexels images (also free)
$alternative_images = [
    'burger' => 'https://images.pexels.com/photos/1639557/pexels-photo-1639557.jpeg?w=800&h=600&fit=crop',
    'pizza' => 'https://images.pexels.com/photos/1653877/pexels-photo-1653877.jpeg?w=800&h=600&fit=crop',
    'pasta' => 'https://images.pexels.com/photos/1279330/pexels-photo-1279330.jpeg?w=800&h=600&fit=crop',
    'salad' => 'https://images.pexels.com/photos/1211887/pexels-photo-1211887.jpeg?w=800&h=600&fit=crop',
    'dessert' => 'https://images.pexels.com/photos/1126359/pexels-photo-1126359.jpeg?w=800&h=600&fit=crop',
    'drink' => 'https://images.pexels.com/photos/1292294/pexels-photo-1292294.jpeg?w=800&h=600&fit=crop',
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product Images - FoodHub</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        h1 { color: #f59e0b; }
        .container { max-width: 1200px; margin: 0 auto; }
        .success { color: green; font-weight: bold; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0; }
        .error { color: red; font-weight: bold; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px 0; }
        .info { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196f3; margin: 20px 0; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }
        .product-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .product-card img { width: 100%; height: 200px; object-fit: cover; }
        .product-info { padding: 15px; }
        .product-name { font-weight: bold; margin-bottom: 5px; }
        .btn { display: inline-block; padding: 12px 24px; margin: 10px 5px; text-decoration: none; border-radius: 5px; font-weight: bold; cursor: pointer; border: none; font-size: 16px; }
        .btn-primary { background: #f59e0b; color: white; }
        .btn-primary:hover { background: #fb923c; }
        .btn-success { background: #28a745; color: white; }
        .step { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖼️ Product Image Updater</h1>
        
        <?php
        $download_images = isset($_GET['download']) && $_GET['download'] == 'yes';
        
        if (!$download_images) {
            // PREVIEW MODE
            echo "<div class='info'>";
            echo "<h2>📋 Preview Mode</h2>";
            echo "<p>This tool will update product images with high-quality photos from Unsplash (free, no attribution required).</p>";
            echo "<p><strong>Images will be downloaded and saved to:</strong> <code>assets/images/food/</code></p>";
            echo "</div>";
            
            // Show current products
            echo "<div class='step'>";
            echo "<h2>Current Products in Database:</h2>";
            $query = "SELECT id, name, image, category_id FROM food_items ORDER BY name";
            $result = mysqli_query($conn, $query);
            
            echo "<div class='product-grid'>";
            while ($product = mysqli_fetch_assoc($result)) {
                $image_url = isset($product_images[$product['name']]) ? $product_images[$product['name']] : '';
                $current_image = $product['image'] ? "assets/images/food/{$product['image']}" : 'assets/images/food/default.jpg';
                
                echo "<div class='product-card'>";
                if ($image_url) {
                    echo "<img src='{$image_url}' alt='{$product['name']}' loading='lazy'>";
                } else {
                    echo "<img src='{$current_image}' alt='{$product['name']}'>";
                }
                echo "<div class='product-info'>";
                echo "<div class='product-name'>{$product['name']}</div>";
                echo "<small>ID: {$product['id']}</small><br>";
                echo "<small>Current: " . ($product['image'] ?: 'default.jpg') . "</small><br>";
                if ($image_url) {
                    echo "<small style='color: green;'>✅ New image available</small>";
                } else {
                    echo "<small style='color: orange;'>⚠️ Using default</small>";
                }
                echo "</div></div>";
            }
            echo "</div>";
            echo "</div>";
            
            // Show mapping
            echo "<div class='step'>";
            echo "<h2>📊 Image Mapping</h2>";
            echo "<table style='width: 100%; background: white; border-collapse: collapse;'>";
            echo "<tr style='background: #f59e0b; color: white;'><th style='padding: 10px;'>Product Name</th><th>Image Source</th><th>Status</th></tr>";
            
            $products_query = "SELECT name FROM food_items ORDER BY name";
            $products_result = mysqli_query($conn, $products_query);
            
            while ($p = mysqli_fetch_assoc($products_result)) {
                $has_image = isset($product_images[$p['name']]);
                $status = $has_image ? "<span style='color: green;'>✅ Ready</span>" : "<span style='color: orange;'>⚠️ Default</span>";
                $source = $has_image ? "Unsplash" : "Default image";
                
                echo "<tr style='border-bottom: 1px solid #ddd;'>";
                echo "<td style='padding: 10px;'><strong>{$p['name']}</strong></td>";
                echo "<td style='padding: 10px;'>{$source}</td>";
                echo "<td style='padding: 10px;'>{$status}</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
            
            // Action button
            echo "<div class='step' style='text-align: center;'>";
            echo "<h2>Ready to Download Images?</h2>";
            echo "<p>Click the button below to download all images and update the database.</p>";
            echo "<a href='update_product_images.php?download=yes' class='btn btn-primary' onclick='return confirm(\"Download and update all product images?\");'>🖼️ DOWNLOAD & UPDATE IMAGES</a>";
            echo "<a href='customer/menu.php' class='btn btn-success'>🍽️ View Menu</a>";
            echo "</div>";
            
        } else {
            // DOWNLOAD AND UPDATE MODE
            echo "<div class='step'>";
            echo "<h2>🔄 Downloading and Updating Images...</h2>";
            
            // Create directory if it doesn't exist
            $image_dir = 'assets/images/food/';
            if (!file_exists($image_dir)) {
                mkdir($image_dir, 0777, true);
                echo "<p>✅ Created directory: {$image_dir}</p>";
            }
            
            $updated_count = 0;
            $failed_count = 0;
            $log = [];
            
            // Get all products
            $query = "SELECT id, name FROM food_items ORDER BY name";
            $result = mysqli_query($conn, $query);
            
            while ($product = mysqli_fetch_assoc($result)) {
                $product_name = $product['name'];
                $product_id = $product['id'];
                
                if (isset($product_images[$product_name])) {
                    $image_url = $product_images[$product_name];
                    
                    // Generate safe filename
                    $safe_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $product_name));
                    $filename = $safe_name . '.jpg';
                    $filepath = $image_dir . $filename;
                    
                    // Download image
                    try {
                        $image_data = @file_get_contents($image_url);
                        
                        if ($image_data !== false) {
                            file_put_contents($filepath, $image_data);
                            
                            // Update database
                            $update_query = "UPDATE food_items SET image = '{$filename}' WHERE id = {$product_id}";
                            if (mysqli_query($conn, $update_query)) {
                                $updated_count++;
                                $log[] = "✅ <strong>{$product_name}</strong>: Downloaded and updated ({$filename})";
                            } else {
                                $failed_count++;
                                $log[] = "❌ <strong>{$product_name}</strong>: Download OK, DB update failed";
                            }
                        } else {
                            $failed_count++;
                            $log[] = "⚠️ <strong>{$product_name}</strong>: Failed to download image";
                        }
                    } catch (Exception $e) {
                        $failed_count++;
                        $log[] = "❌ <strong>{$product_name}</strong>: Error - " . $e->getMessage();
                    }
                    
                    // Small delay to avoid rate limiting
                    usleep(200000); // 0.2 seconds
                } else {
                    $log[] = "⚠️ <strong>{$product_name}</strong>: No image mapping found (using default)";
                }
            }
            
            echo "</div>";
            
            // Show results
            echo "<div class='step'>";
            echo "<h2>📊 Update Results</h2>";
            echo "<div class='success'>";
            echo "<h3>✅ Success!</h3>";
            echo "<p><strong>Images Updated:</strong> {$updated_count}</p>";
            echo "<p><strong>Failed:</strong> {$failed_count}</p>";
            echo "</div>";
            
            echo "<h3>Detailed Log:</h3>";
            echo "<div style='background: #f9f9f9; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto;'>";
            foreach ($log as $entry) {
                echo "<p style='margin: 5px 0;'>{$entry}</p>";
            }
            echo "</div>";
            echo "</div>";
            
            // Preview updated menu
            echo "<div class='step'>";
            echo "<h2>🎨 Updated Products Preview:</h2>";
            $preview_query = "SELECT id, name, image FROM food_items ORDER BY name LIMIT 12";
            $preview_result = mysqli_query($conn, $preview_query);
            
            echo "<div class='product-grid'>";
            while ($product = mysqli_fetch_assoc($preview_result)) {
                $image_path = $product['image'] ? "assets/images/food/{$product['image']}" : 'assets/images/food/default.jpg';
                
                echo "<div class='product-card'>";
                echo "<img src='../{$image_path}' alt='{$product['name']}' onerror=\"this.src='../assets/images/food/default.jpg'\">";
                echo "<div class='product-info'>";
                echo "<div class='product-name'>{$product['name']}</div>";
                echo "<small>{$product['image']}</small>";
                echo "</div></div>";
            }
            echo "</div>";
            echo "</div>";
            
            // Next steps
            echo "<div class='step' style='text-align: center;'>";
            echo "<h2>🎉 Images Updated Successfully!</h2>";
            echo "<p>All product images have been downloaded and updated in the database.</p>";
            echo "<a href='customer/menu.php' class='btn btn-success'>🍽️ View Updated Menu</a>";
            echo "<a href='update_product_images.php' class='btn btn-primary'>🔄 Run Again</a>";
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>
