<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Manager - Menu Images</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php
    require_once 'db.php';
    
    // Get all products
    $query = "SELECT f.id, f.name, f.image, f.price, c.name as category_name 
              FROM food_items f 
              LEFT JOIN categories c ON f.category_id = c.id 
              ORDER BY f.id";
    $result = mysqli_query($conn, $query);
    
    // Get all image files
    $image_folder = 'assets/images/food/';
    $all_files = [];
    if (is_dir($image_folder)) {
        $files = scandir($image_folder);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && !is_dir($image_folder . $file)) {
                $all_files[] = $file;
            }
        }
    }
    ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-2xl shadow-xl p-8 mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">
                <i class="fas fa-images mr-3"></i>Menu Image Manager
            </h1>
            <p class="text-white/90 text-lg">View and manage your product images</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Products</p>
                        <p class="text-3xl font-bold text-orange-500"><?php echo mysqli_num_rows($result); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-utensils text-2xl text-orange-500"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Image Files</p>
                        <p class="text-3xl font-bold text-blue-500"><?php echo count($all_files); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-image text-2xl text-blue-500"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Folder Path</p>
                        <p class="text-sm font-semibold text-gray-700 mt-1">assets/images/food/</p>
                    </div>
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-folder text-2xl text-green-500"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Products Section -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-list mr-3 text-orange-500"></i>Current Products & Images
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php 
                mysqli_data_seek($result, 0); // Reset pointer
                while ($product = mysqli_fetch_assoc($result)): 
                    $image_path = 'assets/images/food/' . ($product['image'] ?? 'default.jpg');
                    $image_exists = file_exists($image_path);
                ?>
                <div class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300">
                    <!-- Image -->
                    <div class="relative h-48 bg-gray-100">
                        <?php if ($image_exists): ?>
                            <img src="<?php echo $image_path; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 class="w-full h-full object-cover"
                                 onerror="this.src='assets/images/food/default.jpg'">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                <i class="fas fa-image text-4xl text-gray-400"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Status Badge -->
                        <div class="absolute top-2 right-2">
                            <?php if ($image_exists): ?>
                                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                    <i class="fas fa-check mr-1"></i>OK
                                </span>
                            <?php else: ?>
                                <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                    <i class="fas fa-times mr-1"></i>Missing
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Info -->
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-bold text-gray-800 text-lg"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <span class="text-orange-500 font-bold">₱<?php echo number_format($product['price'], 2); ?></span>
                        </div>
                        
                        <p class="text-gray-600 text-sm mb-3">
                            <i class="fas fa-tag mr-1"></i><?php echo htmlspecialchars($product['category_name']); ?>
                        </p>
                        
                        <div class="bg-gray-50 rounded-lg p-3 space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">ID:</span>
                                <span class="font-mono bg-gray-200 px-2 py-1 rounded"><?php echo $product['id']; ?></span>
                            </div>
                            <div class="flex items-start justify-between text-sm">
                                <span class="text-gray-600">Image:</span>
                                <span class="font-mono text-xs bg-gray-200 px-2 py-1 rounded break-all ml-2">
                                    <?php echo $product['image'] ?? 'NULL'; ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Update Button -->
                        <button onclick="showUpdateSQL(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>')" 
                                class="w-full mt-3 bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg font-semibold transition-colors">
                            <i class="fas fa-code mr-2"></i>Show Update SQL
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Available Files Section -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-folder-open mr-3 text-blue-500"></i>Available Image Files
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($all_files as $file): 
                    $file_path = $image_folder . $file;
                    $file_size = filesize($file_path);
                    $file_size_kb = round($file_size / 1024, 2);
                    
                    // Check if file is used in database
                    $check_query = "SELECT COUNT(*) as count FROM food_items WHERE image = '" . mysqli_real_escape_string($conn, $file) . "'";
                    $check_result = mysqli_query($conn, $check_query);
                    $is_used = mysqli_fetch_assoc($check_result)['count'] > 0;
                ?>
                <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-all">
                    <div class="h-32 bg-gray-100 relative">
                        <img src="<?php echo $file_path; ?>" 
                             alt="<?php echo $file; ?>"
                             class="w-full h-full object-cover"
                             onerror="this.src='assets/images/food/default.jpg'">
                        
                        <div class="absolute top-1 right-1">
                            <?php if ($is_used): ?>
                                <span class="bg-green-500 text-white px-2 py-1 rounded text-xs font-bold">
                                    <i class="fas fa-check"></i>
                                </span>
                            <?php else: ?>
                                <span class="bg-gray-500 text-white px-2 py-1 rounded text-xs font-bold">
                                    <i class="fas fa-minus"></i>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="p-3">
                        <p class="text-xs font-mono text-gray-700 truncate mb-1" title="<?php echo $file; ?>">
                            <?php echo $file; ?>
                        </p>
                        <p class="text-xs text-gray-500"><?php echo $file_size_kb; ?> KB</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quick Guide -->
        <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-lightbulb mr-3 text-yellow-500"></i>Quick Update Guide
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl p-6">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                        <span class="text-xl font-bold text-orange-500">1</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Upload Image</h3>
                    <p class="text-gray-600 text-sm mb-3">Copy your image file to:</p>
                    <code class="bg-gray-100 px-3 py-2 rounded text-xs block break-all">
                        C:\xampp\htdocs\Food_Ordering\assets\images\food\
                    </code>
                </div>
                
                <div class="bg-white rounded-xl p-6">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <span class="text-xl font-bold text-blue-500">2</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Update Database</h3>
                    <p class="text-gray-600 text-sm mb-3">Click "Show Update SQL" button above to get the query for each product</p>
                </div>
                
                <div class="bg-white rounded-xl p-6">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <span class="text-xl font-bold text-green-500">3</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Verify Changes</h3>
                    <p class="text-gray-600 text-sm mb-3">Refresh this page and the menu to see your new images!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SQL Modal -->
    <div id="sqlModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-gray-800">Update SQL Query</h3>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <p class="text-gray-700 mb-4">
                    Product: <strong id="modalProductName"></strong> (ID: <strong id="modalProductId"></strong>)
                </p>
                
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    New Image Filename:
                </label>
                <input type="text" 
                       id="newImageName" 
                       placeholder="e.g., burger-deluxe.jpg"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 mb-4">
                
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    SQL Query to Run:
                </label>
                <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm overflow-x-auto mb-4">
                    <code id="sqlQuery">UPDATE food_items SET image = 'your-image.jpg' WHERE id = 1;</code>
                </div>
                
                <div class="flex gap-3">
                    <button onclick="copySQLToClipboard()" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-semibold transition-colors">
                        <i class="fas fa-copy mr-2"></i>Copy SQL
                    </button>
                    <a href="http://localhost/phpmyadmin" target="_blank" 
                       class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-lg font-semibold text-center transition-colors">
                        <i class="fas fa-external-link-alt mr-2"></i>Open phpMyAdmin
                    </a>
                </div>
                
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Steps:</strong> Enter your image filename above → Copy the SQL → 
                        Open phpMyAdmin → Go to SQL tab → Paste → Click Go
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showUpdateSQL(productId, productName) {
            document.getElementById('modalProductId').textContent = productId;
            document.getElementById('modalProductName').textContent = productName;
            document.getElementById('sqlModal').classList.remove('hidden');
            updateSQL();
        }
        
        function closeModal() {
            document.getElementById('sqlModal').classList.add('hidden');
        }
        
        function updateSQL() {
            const productId = document.getElementById('modalProductId').textContent;
            const imageName = document.getElementById('newImageName').value || 'your-image.jpg';
            const sql = `UPDATE food_items SET image = '${imageName}' WHERE id = ${productId};`;
            document.getElementById('sqlQuery').textContent = sql;
        }
        
        document.getElementById('newImageName').addEventListener('input', updateSQL);
        
        function copySQLToClipboard() {
            const sql = document.getElementById('sqlQuery').textContent;
            navigator.clipboard.writeText(sql).then(() => {
                alert('✅ SQL query copied to clipboard!');
            });
        }
        
        // Close modal on outside click
        document.getElementById('sqlModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
