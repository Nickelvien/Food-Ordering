<?php
require_once '../db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify Images</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">📸 Image Verification</h1>
        
        <?php
        $query = "SELECT id, name, image, category_id FROM food_items ORDER BY category_id, id";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            echo "<p class='text-green-700 font-bold mb-4'>✓ Found " . mysqli_num_rows($result) . " food items</p>";
            echo "<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4'>";
            
            while ($item = mysqli_fetch_assoc($result)) {
                $image_src = $item['image'];
                if (!empty($image_src) && !filter_var($image_src, FILTER_VALIDATE_URL)) {
                    $image_src = '../assets/images/food/' . $image_src;
                }
                
                echo "<div class='bg-white p-4 rounded-lg shadow'>";
                echo "<img src='" . htmlspecialchars($image_src) . "' class='w-full h-40 object-cover rounded mb-2' onerror=\"this.src='https://via.placeholder.com/400x300?text=Error'\">";
                echo "<h3 class='font-bold'>{$item['name']}</h3>";
                echo "<p class='text-xs text-gray-500 break-all'>{$item['image']}</p>";
                
                // Check if it's a URL
                if (filter_var($item['image'], FILTER_VALIDATE_URL)) {
                    echo "<p class='text-green-600 text-xs mt-1'>✓ Valid URL</p>";
                } else {
                    echo "<p class='text-yellow-600 text-xs mt-1'>⚠ Local file</p>";
                }
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'>";
            echo "<p class='font-bold'>❌ No food items found!</p>";
            echo "<p>Database might be empty. Run complete_setup.php first.</p>";
            echo "<a href='complete_setup.php' class='inline-block mt-2 bg-red-600 text-white px-4 py-2 rounded'>Run Setup</a>";
            echo "</div>";
        }
        ?>
        
        <div class="mt-8 space-x-4">
            <a href="../customer/menu.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded">View Menu</a>
            <a href="complete_setup.php" class="inline-block bg-green-600 text-white px-6 py-2 rounded">Run Setup</a>
        </div>
    </div>
</body>
</html>
