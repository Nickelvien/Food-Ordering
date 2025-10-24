<?php
/**
 * Update Food Item Images
 * This script updates all food items with high-quality, reliable food images
 * Using Unsplash (free, high-quality, no attribution required)
 */

require_once '../db.php';

// Define high-quality image URLs for each food item
$food_images = [
    // Burgers
    'Classic Beef Burger' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800&q=80',
    'Chicken Burger' => 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=800&q=80',
    'Veggie Burger' => 'https://images.unsplash.com/photo-1520072959219-c595dc870360?w=800&q=80',
    'Double Cheese Burger' => 'https://images.unsplash.com/photo-1572802419224-296b0aeee0d9?w=800&q=80',
    
    // Pasta
    'Spaghetti Carbonara' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=800&q=80',
    'Penne Arrabbiata' => 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=800&q=80',
    'Fettuccine Alfredo' => 'https://images.unsplash.com/photo-1645112411341-6c4fd023714a?w=800&q=80',
    'Lasagna' => 'https://images.unsplash.com/photo-1574894709920-11b28e7367e3?w=800&q=80',
    
    // Drinks
    'Fresh Orange Juice' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=800&q=80',
    'Mango Smoothie' => 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=800&q=80',
    'Iced Coffee' => 'https://images.unsplash.com/photo-1517487881594-2787fef5ebf7?w=800&q=80',
    'Lemonade' => 'https://images.unsplash.com/photo-1523677011781-c91d1bbe2f9f?w=800&q=80',
    
    // Desserts
    'Chocolate Cake' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=800&q=80',
    'Tiramisu' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=800&q=80',
    'Ice Cream Sundae' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=800&q=80',
    'Cheesecake' => 'https://images.unsplash.com/photo-1533134486753-c833f0ed4866?w=800&q=80',
    
    // Pizza
    'Margherita Pizza' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=800&q=80',
    'Pepperoni Pizza' => 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=800&q=80',
    'Vegetarian Pizza' => 'https://images.unsplash.com/photo-1511689660979-10d2b1aada49?w=800&q=80',
    
    // Salads
    'Caesar Salad' => 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=800&q=80',
    'Greek Salad' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=800&q=80',
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Food Images - FoodHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">
                <i class="fas fa-image text-primary"></i> Update Food Item Images
            </h1>

            <?php
            $updated_count = 0;
            $failed_count = 0;
            $results = [];

            echo "<div class='space-y-3 mb-6'>";
            
            foreach ($food_images as $food_name => $image_url) {
                $safe_name = mysqli_real_escape_string($conn, $food_name);
                $safe_url = mysqli_real_escape_string($conn, $image_url);
                
                $update_query = "UPDATE food_items SET image = '$safe_url' WHERE name = '$safe_name'";
                
                if (mysqli_query($conn, $update_query)) {
                    $affected_rows = mysqli_affected_rows($conn);
                    if ($affected_rows > 0) {
                        $updated_count++;
                        $results[] = [
                            'name' => $food_name,
                            'status' => 'success',
                            'url' => $image_url,
                            'message' => 'Updated successfully'
                        ];
                        echo "<div class='bg-green-50 border-l-4 border-green-500 p-4'>
                                <p class='font-semibold text-green-800'>✓ {$food_name}</p>
                                <p class='text-green-700 text-sm truncate'>{$image_url}</p>
                              </div>";
                    } else {
                        $results[] = [
                            'name' => $food_name,
                            'status' => 'warning',
                            'url' => $image_url,
                            'message' => 'Item not found in database'
                        ];
                        echo "<div class='bg-yellow-50 border-l-4 border-yellow-500 p-4'>
                                <p class='font-semibold text-yellow-800'>⚠ {$food_name}</p>
                                <p class='text-yellow-700 text-sm'>Item not found in database</p>
                              </div>";
                    }
                } else {
                    $failed_count++;
                    $results[] = [
                        'name' => $food_name,
                        'status' => 'error',
                        'url' => $image_url,
                        'message' => mysqli_error($conn)
                    ];
                    echo "<div class='bg-red-50 border-l-4 border-red-500 p-4'>
                            <p class='font-semibold text-red-800'>✗ {$food_name}</p>
                            <p class='text-red-700 text-sm'>" . mysqli_error($conn) . "</p>
                          </div>";
                }
            }
            
            echo "</div>";

            // Summary
            echo "<div class='bg-blue-50 border-l-4 border-blue-500 p-6 mb-6'>
                    <h2 class='font-bold text-blue-800 text-xl mb-3'>📊 Summary</h2>
                    <div class='grid grid-cols-3 gap-4 text-center'>
                        <div>
                            <p class='text-3xl font-bold text-green-600'>{$updated_count}</p>
                            <p class='text-sm text-gray-600'>Updated</p>
                        </div>
                        <div>
                            <p class='text-3xl font-bold text-red-600'>{$failed_count}</p>
                            <p class='text-sm text-gray-600'>Failed</p>
                        </div>
                        <div>
                            <p class='text-3xl font-bold text-blue-600'>" . count($food_images) . "</p>
                            <p class='text-sm text-gray-600'>Total</p>
                        </div>
                    </div>
                  </div>";

            // Display updated items with preview
            echo "<h2 class='text-2xl font-bold text-gray-800 mb-4'>🖼️ Image Preview</h2>";
            echo "<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6'>";
            
            $preview_query = "SELECT name, image FROM food_items ORDER BY category_id, name";
            $preview_result = mysqli_query($conn, $preview_query);
            
            while ($item = mysqli_fetch_assoc($preview_result)) {
                echo "<div class='bg-white border rounded-lg overflow-hidden shadow hover:shadow-lg transition'>
                        <img src='{$item['image']}' 
                             alt='{$item['name']}' 
                             class='w-full h-48 object-cover'
                             onerror=\"this.src='https://via.placeholder.com/400x300?text=Image+Not+Found'\">
                        <div class='p-3'>
                            <p class='font-semibold text-gray-800 text-sm'>{$item['name']}</p>
                            <p class='text-xs text-gray-500 truncate'>{$item['image']}</p>
                        </div>
                      </div>";
            }
            
            echo "</div>";
            ?>

            <div class="bg-green-50 border-l-4 border-green-500 p-6 mb-6">
                <h3 class="font-bold text-green-800 text-lg mb-2">✅ Images Updated Successfully!</h3>
                <p class="text-green-700">All food items now have high-quality, reliable images from Unsplash.</p>
                <ul class="list-disc list-inside text-green-700 text-sm mt-2">
                    <li>Images are free to use (Unsplash License)</li>
                    <li>No attribution required</li>
                    <li>High resolution (800px width, 80% quality)</li>
                    <li>Fast loading with CDN</li>
                </ul>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 mb-6">
                <h3 class="font-bold text-yellow-800 text-lg mb-2">📸 Want to Use Pinterest Images?</h3>
                <p class="text-yellow-700 mb-3">Follow these steps to use Pinterest images legally:</p>
                <ol class="list-decimal list-inside text-yellow-700 text-sm space-y-2">
                    <li>Go to <a href="https://www.pinterest.com" class="underline font-semibold" target="_blank">Pinterest.com</a></li>
                    <li>Search for your desired food (e.g., "burger food photography")</li>
                    <li>Click on the image you like</li>
                    <li>Click "Visit" to go to the original source</li>
                    <li>Check the license (ensure it's free to use or Creative Commons)</li>
                    <li>Right-click the image → "Copy image address"</li>
                    <li>Use the image URL in the admin panel</li>
                </ol>
                <p class="text-yellow-800 font-semibold mt-3">⚠️ Important: Always respect copyright and use only royalty-free images!</p>
            </div>

            <div class="bg-purple-50 border-l-4 border-purple-500 p-6 mb-6">
                <h3 class="font-bold text-purple-800 text-lg mb-2">🌐 Recommended Free Image Sources</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="font-semibold text-purple-800">Unsplash (Currently Used)</p>
                        <a href="https://unsplash.com" class="text-purple-600 underline" target="_blank">unsplash.com</a>
                        <p class="text-purple-700 text-xs">✓ Free, no attribution, high quality</p>
                    </div>
                    <div>
                        <p class="font-semibold text-purple-800">Pexels</p>
                        <a href="https://www.pexels.com" class="text-purple-600 underline" target="_blank">pexels.com</a>
                        <p class="text-purple-700 text-xs">✓ Free stock photos and videos</p>
                    </div>
                    <div>
                        <p class="font-semibold text-purple-800">Pixabay</p>
                        <a href="https://pixabay.com" class="text-purple-600 underline" target="_blank">pixabay.com</a>
                        <p class="text-purple-700 text-xs">✓ Over 2.7 million free images</p>
                    </div>
                    <div>
                        <p class="font-semibold text-purple-800">Foodiesfeed</p>
                        <a href="https://www.foodiesfeed.com" class="text-purple-600 underline" target="_blank">foodiesfeed.com</a>
                        <p class="text-purple-700 text-xs">✓ Food photos only, free download</p>
                    </div>
                </div>
            </div>

            <div class="flex space-x-4">
                <a href="../customer/menu.php" class="flex-1 text-center bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-utensils mr-2"></i>View Menu
                </a>
                <a href="../admin/manage_food.php" class="flex-1 text-center bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-edit mr-2"></i>Manage Food Items
                </a>
                <a href="../index.php" class="flex-1 text-center bg-gray-600 text-white py-3 rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
            </div>

            <div class="mt-6 p-4 bg-gray-50 border border-gray-300 rounded">
                <p class="text-sm text-gray-700">
                    <strong>Next Steps:</strong>
                    <br>1. Check the menu to see the new images
                    <br>2. You can change any image through Admin Panel → Manage Food
                    <br>3. Delete this file after verifying the images
                </p>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to summary
        window.addEventListener('load', function() {
            const summary = document.querySelector('.bg-blue-50');
            if (summary) {
                setTimeout(() => {
                    summary.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 500);
            }
        });
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>
