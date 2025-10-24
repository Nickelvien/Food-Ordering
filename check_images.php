<?php
require_once 'db.php';

echo "======================================================\n";
echo "CURRENT PRODUCT IMAGES IN DATABASE\n";
echo "======================================================\n\n";

$query = "SELECT id, name, image, category_id FROM food_items ORDER BY id";
$result = mysqli_query($conn, $query);

if ($result) {
    printf("%-5s | %-35s | %-30s\n", "ID", "Product Name", "Current Image");
    echo str_repeat("-", 80) . "\n";
    
    while ($row = mysqli_fetch_assoc($result)) {
        printf("%-5s | %-35s | %-30s\n", 
            $row['id'], 
            substr($row['name'], 0, 35), 
            $row['image'] ?? 'NULL'
        );
    }
    
    echo "\n======================================================\n";
    echo "AVAILABLE IMAGE FILES IN FOLDER\n";
    echo "======================================================\n\n";
    
    $image_folder = 'assets/images/food/';
    $files = scandir($image_folder);
    
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && !is_dir($image_folder . $file)) {
            $exists_in_db = mysqli_query($conn, "SELECT COUNT(*) as count FROM food_items WHERE image = '$file'");
            $count_row = mysqli_fetch_assoc($exists_in_db);
            $status = $count_row['count'] > 0 ? '✓ Used' : '✗ Unused';
            printf("%-35s | %s\n", $file, $status);
        }
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
