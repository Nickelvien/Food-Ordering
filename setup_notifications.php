<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Notifications - Gourmet Sentinel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">
                <i class="fas fa-bell text-orange-500"></i> Setup Notifications System
            </h1>
            
            <?php
            require_once 'db.php';
            
            echo "<div class='mb-6'>";
            echo "<h2 class='text-xl font-semibold mb-4'>Creating notifications table...</h2>";
            
            // Create notifications table
            $sql = "CREATE TABLE IF NOT EXISTS notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                order_id INT NOT NULL,
                message TEXT NOT NULL,
                type ENUM('new_order', 'processing', 'delivered', 'cancelled') NOT NULL,
                is_read TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                INDEX idx_user_read (user_id, is_read),
                INDEX idx_created (created_at DESC)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            if (mysqli_query($conn, $sql)) {
                echo "<p class='text-green-600'><i class='fas fa-check-circle'></i> Notifications table created successfully!</p>";
            } else {
                echo "<p class='text-red-600'><i class='fas fa-times-circle'></i> Error: " . mysqli_error($conn) . "</p>";
            }
            
            echo "</div>";
            
            echo "<div class='bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6'>";
            echo "<h3 class='font-semibold text-blue-800 mb-2'>Notification Features:</h3>";
            echo "<ul class='list-disc list-inside text-blue-700 space-y-1'>";
            echo "<li>Admin gets notified when new orders are placed</li>";
            echo "<li>Customers get notified when order status changes</li>";
            echo "<li>Real-time notification counter</li>";
            echo "<li>Mark notifications as read/unread</li>";
            echo "</ul>";
            echo "</div>";
            
            echo "<div class='mt-6'>";
            echo "<a href='index.php' class='bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600 inline-block'>
                    <i class='fas fa-home mr-2'></i> Back to Home
                  </a>";
            echo "</div>";
            ?>
        </div>
    </div>
</body>
</html>
