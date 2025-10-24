<?php
require_once 'db.php';

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Setup Notification System - Gourmet Sentinel</title>";
echo "<style>
    body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
    .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h1 { color: #f59e0b; }
    .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }
    .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc3545; }
    .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #17a2b8; }
    .button { display: inline-block; background: #f59e0b; color: white; padding: 12px 24px; border-radius: 5px; text-decoration: none; margin: 10px 5px 0 0; }
    .button:hover { background: #fb923c; }
    code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
</style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";
echo "<h1><i class='fas fa-bell'></i> Notification System Setup</h1>";

// SQL to create notifications table
$sql = "CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_role` VARCHAR(20) NOT NULL,
  `user_id` INT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `related_id` INT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user_role (user_role),
  INDEX idx_user_id (user_id),
  INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// Execute the SQL
if (mysqli_query($conn, $sql)) {
    echo "<div class='success'>";
    echo "<strong>✓ Success!</strong> Notifications table created successfully.";
    echo "</div>";
    
    // Check if table exists
    $check = mysqli_query($conn, "SHOW TABLES LIKE 'notifications'");
    if (mysqli_num_rows($check) > 0) {
        echo "<div class='info'>";
        echo "<strong>Table Structure:</strong><br>";
        echo "• <code>id</code> - Auto-increment primary key<br>";
        echo "• <code>user_role</code> - 'admin' or 'user'<br>";
        echo "• <code>user_id</code> - User ID for user-specific notifications<br>";
        echo "• <code>title</code> - Notification title<br>";
        echo "• <code>message</code> - Notification message<br>";
        echo "• <code>related_id</code> - Related order ID<br>";
        echo "• <code>is_read</code> - Read status (0 = unread, 1 = read)<br>";
        echo "• <code>created_at</code> - Timestamp<br>";
        echo "</div>";
        
        echo "<div class='success'>";
        echo "<strong>Next Steps:</strong><br>";
        echo "1. Admin notifications will appear when customers place orders<br>";
        echo "2. Customer notifications will appear when order status changes<br>";
        echo "3. Check the bell icon in the admin and customer navigation<br>";
        echo "4. Test by placing an order and updating its status<br>";
        echo "</div>";
    }
    
} else {
    echo "<div class='error'>";
    echo "<strong>✗ Error:</strong> " . mysqli_error($conn);
    echo "</div>";
}

echo "<br>";
echo "<a href='admin/dashboard.php' class='button'>Go to Admin Dashboard</a>";
echo "<a href='customer/menu.php' class='button'>Go to Menu</a>";
echo "<a href='index.php' class='button'>Go to Home</a>";

echo "</div>";
echo "</body>";
echo "</html>";

mysqli_close($conn);
?>
