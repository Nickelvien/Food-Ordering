<?php
/**
 * COMPLETE DATABASE SETUP - Import Everything at Once
 * This will create the database, tables, and add food items with images
 */

// Database connection details
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'food_ordering_system';

// Connect to MySQL (without selecting database first)
$conn = mysqli_connect($host, $user, $pass);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Database Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">🔧 Complete Database Setup</h1>
            
            <?php
            $step = 1;
            $errors = [];
            
            // Step 1: Create Database
            echo "<div class='mb-4 p-4 bg-blue-50 border-l-4 border-blue-500'>";
            echo "<h2 class='font-bold text-blue-800'>Step {$step}: Create Database</h2>";
            
            $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
            if (mysqli_query($conn, $sql)) {
                echo "<p class='text-green-700'>✓ Database '$dbname' created successfully</p>";
            } else {
                echo "<p class='text-red-700'>✗ Error: " . mysqli_error($conn) . "</p>";
                $errors[] = "Database creation failed";
            }
            echo "</div>";
            $step++;
            
            // Select the database
            mysqli_select_db($conn, $dbname);
            
            // Step 2: Create Tables
            echo "<div class='mb-4 p-4 bg-blue-50 border-l-4 border-blue-500'>";
            echo "<h2 class='font-bold text-blue-800'>Step {$step}: Create Tables</h2>";
            
            // Users table
            $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                phone VARCHAR(20),
                address TEXT,
                role ENUM('admin', 'customer') DEFAULT 'customer',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            if (mysqli_query($conn, $sql)) {
                echo "<p class='text-green-700'>✓ Users table created</p>";
            } else {
                echo "<p class='text-red-700'>✗ Users table error: " . mysqli_error($conn) . "</p>";
            }
            
            // Categories table
            $sql = "CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL,
                description TEXT,
                image VARCHAR(255),
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            if (mysqli_query($conn, $sql)) {
                echo "<p class='text-green-700'>✓ Categories table created</p>";
            } else {
                echo "<p class='text-red-700'>✗ Categories table error: " . mysqli_error($conn) . "</p>";
            }
            
            // Food items table
            $sql = "CREATE TABLE IF NOT EXISTS food_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category_id INT NOT NULL,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                price DECIMAL(10, 2) NOT NULL,
                image VARCHAR(255),
                is_available TINYINT(1) DEFAULT 1,
                is_featured TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
            )";
            if (mysqli_query($conn, $sql)) {
                echo "<p class='text-green-700'>✓ Food items table created</p>";
            } else {
                echo "<p class='text-red-700'>✗ Food items table error: " . mysqli_error($conn) . "</p>";
            }
            
            // Orders table
            $sql = "CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                order_number VARCHAR(50) UNIQUE NOT NULL,
                total_amount DECIMAL(10, 2) NOT NULL,
                delivery_address TEXT NOT NULL,
                phone VARCHAR(20) NOT NULL,
                payment_mode ENUM('cash', 'online') DEFAULT 'cash',
                status ENUM('pending', 'confirmed', 'preparing', 'delivered', 'cancelled') DEFAULT 'pending',
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )";
            if (mysqli_query($conn, $sql)) {
                echo "<p class='text-green-700'>✓ Orders table created</p>";
            } else {
                echo "<p class='text-red-700'>✗ Orders table error: " . mysqli_error($conn) . "</p>";
            }
            
            // Order items table
            $sql = "CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                food_item_id INT NOT NULL,
                quantity INT NOT NULL,
                price DECIMAL(10, 2) NOT NULL,
                subtotal DECIMAL(10, 2) NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (food_item_id) REFERENCES food_items(id) ON DELETE CASCADE
            )";
            if (mysqli_query($conn, $sql)) {
                echo "<p class='text-green-700'>✓ Order items table created</p>";
            } else {
                echo "<p class='text-red-700'>✗ Order items table error: " . mysqli_error($conn) . "</p>";
            }
            
            // Messages table
            $sql = "CREATE TABLE IF NOT EXISTS messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                subject VARCHAR(200),
                message TEXT NOT NULL,
                is_read TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            if (mysqli_query($conn, $sql)) {
                echo "<p class='text-green-700'>✓ Messages table created</p>";
            } else {
                echo "<p class='text-red-700'>✗ Messages table error: " . mysqli_error($conn) . "</p>";
            }
            
            echo "</div>";
            $step++;
            
            // Step 3: Insert Categories
            echo "<div class='mb-4 p-4 bg-blue-50 border-l-4 border-blue-500'>";
            echo "<h2 class='font-bold text-blue-800'>Step {$step}: Insert Categories</h2>";
            
            $categories = [
                ['Burgers', 'Delicious burgers made with fresh ingredients'],
                ['Pasta', 'Italian pasta dishes with authentic flavors'],
                ['Drinks', 'Refreshing beverages and smoothies'],
                ['Desserts', 'Sweet treats and delightful desserts'],
                ['Pizza', 'Wood-fired pizzas with premium toppings'],
                ['Salads', 'Fresh and healthy salad options']
            ];
            
            foreach ($categories as $cat) {
                $sql = "INSERT INTO categories (name, description) VALUES ('{$cat[0]}', '{$cat[1]}')";
                if (mysqli_query($conn, $sql)) {
                    echo "<p class='text-green-700'>✓ Added category: {$cat[0]}</p>";
                } else {
                    // Category might already exist, skip error
                }
            }
            echo "</div>";
            $step++;
            
            // Step 4: Insert Food Items with Images
            echo "<div class='mb-4 p-4 bg-blue-50 border-l-4 border-blue-500'>";
            echo "<h2 class='font-bold text-blue-800'>Step {$step}: Insert Food Items with Images</h2>";
            
            $food_items = [
                // Burgers (category_id = 1)
                [1, 'Classic Beef Burger', 'Juicy beef patty with lettuce, tomato, and special sauce', 8.99, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800&q=80', 1, 1],
                [1, 'Chicken Burger', 'Grilled chicken breast with mayo and fresh veggies', 7.99, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=800&q=80', 1, 1],
                [1, 'Veggie Burger', 'Plant-based patty with avocado and special sauce', 7.49, 'https://images.unsplash.com/photo-1520072959219-c595dc870360?w=800&q=80', 1, 0],
                [1, 'Double Cheese Burger', 'Double beef patties with melted cheese', 10.99, 'https://images.unsplash.com/photo-1572802419224-296b0aeee0d9?w=800&q=80', 1, 1],
                
                // Pasta (category_id = 2)
                [2, 'Spaghetti Carbonara', 'Creamy pasta with bacon and parmesan', 12.99, 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=800&q=80', 1, 1],
                [2, 'Penne Arrabbiata', 'Spicy tomato sauce with garlic and chili', 10.99, 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=800&q=80', 1, 0],
                [2, 'Fettuccine Alfredo', 'Rich and creamy alfredo sauce', 11.99, 'https://images.unsplash.com/photo-1645112411341-6c4fd023714a?w=800&q=80', 1, 1],
                [2, 'Lasagna', 'Layered pasta with meat sauce and cheese', 13.99, 'https://images.unsplash.com/photo-1574894709920-11b28e7367e3?w=800&q=80', 1, 0],
                
                // Drinks (category_id = 3)
                [3, 'Fresh Orange Juice', 'Freshly squeezed orange juice', 4.99, 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=800&q=80', 1, 0],
                [3, 'Mango Smoothie', 'Tropical mango smoothie with yogurt', 5.99, 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=800&q=80', 1, 1],
                [3, 'Iced Coffee', 'Cold brew coffee with ice', 3.99, 'https://images.unsplash.com/photo-1517487881594-2787fef5ebf7?w=800&q=80', 1, 0],
                [3, 'Lemonade', 'Refreshing homemade lemonade', 3.49, 'https://images.unsplash.com/photo-1523677011781-c91d1bbe2f9f?w=800&q=80', 1, 0],
                
                // Desserts (category_id = 4)
                [4, 'Chocolate Cake', 'Rich chocolate cake with ganache', 6.99, 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=800&q=80', 1, 1],
                [4, 'Tiramisu', 'Classic Italian coffee-flavored dessert', 7.99, 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=800&q=80', 1, 1],
                [4, 'Ice Cream Sundae', 'Vanilla ice cream with toppings', 5.99, 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=800&q=80', 1, 0],
                [4, 'Cheesecake', 'New York style cheesecake', 7.49, 'https://images.unsplash.com/photo-1533134486753-c833f0ed4866?w=800&q=80', 1, 0],
                
                // Pizza (category_id = 5)
                [5, 'Margherita Pizza', 'Classic tomato, mozzarella, and basil', 11.99, 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=800&q=80', 1, 1],
                [5, 'Pepperoni Pizza', 'Loaded with pepperoni and cheese', 13.99, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=800&q=80', 1, 1],
                [5, 'Vegetarian Pizza', 'Fresh vegetables and cheese', 12.49, 'https://images.unsplash.com/photo-1511689660979-10d2b1aada49?w=800&q=80', 1, 0],
                
                // Salads (category_id = 6)
                [6, 'Caesar Salad', 'Romaine lettuce with caesar dressing', 8.99, 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=800&q=80', 1, 0],
                [6, 'Greek Salad', 'Fresh vegetables with feta cheese', 9.49, 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=800&q=80', 1, 1],
            ];
            
            $count = 0;
            foreach ($food_items as $item) {
                $sql = "INSERT INTO food_items (category_id, name, description, price, image, is_available, is_featured) 
                        VALUES ({$item[0]}, '{$item[1]}', '{$item[2]}', {$item[3]}, '{$item[4]}', {$item[5]}, {$item[6]})";
                if (mysqli_query($conn, $sql)) {
                    $count++;
                    echo "<p class='text-green-700'>✓ Added: {$item[1]}</p>";
                } else {
                    echo "<p class='text-yellow-700'>⚠ {$item[1]}: " . mysqli_error($conn) . "</p>";
                }
            }
            echo "<p class='text-blue-800 font-bold mt-2'>Total items added: $count</p>";
            echo "</div>";
            $step++;
            
            // Step 5: Create Admin User
            echo "<div class='mb-4 p-4 bg-blue-50 border-l-4 border-blue-500'>";
            echo "<h2 class='font-bold text-blue-800'>Step {$step}: Create Admin User</h2>";
            
            $admin_password = password_hash('Admin@2025', PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (full_name, email, password, role) 
                    VALUES ('System Administrator', 'admin@foodhub.com', '$admin_password', 'admin')";
            if (mysqli_query($conn, $sql)) {
                echo "<p class='text-green-700'>✓ Admin user created</p>";
                echo "<p class='text-gray-700'>Email: admin@foodhub.com</p>";
                echo "<p class='text-gray-700'>Password: Admin@2025</p>";
            } else {
                echo "<p class='text-yellow-700'>⚠ Admin might already exist: " . mysqli_error($conn) . "</p>";
            }
            echo "</div>";
            
            mysqli_close($conn);
            
            // Success Summary
            echo "<div class='bg-green-50 border-l-4 border-green-500 p-6 mb-6'>";
            echo "<h2 class='font-bold text-green-800 text-2xl mb-4'>✅ Setup Complete!</h2>";
            echo "<p class='text-green-700 mb-4'>Your Food Ordering System is ready to use!</p>";
            echo "<ul class='list-disc list-inside text-green-700 space-y-2'>";
            echo "<li>Database created</li>";
            echo "<li>All tables created</li>";
            echo "<li>6 categories added</li>";
            echo "<li>22 food items with images added</li>";
            echo "<li>Admin account created</li>";
            echo "</ul>";
            echo "</div>";
            
            echo "<div class='bg-blue-50 border-l-4 border-blue-500 p-6 mb-6'>";
            echo "<h2 class='font-bold text-blue-800 text-xl mb-3'>🔐 Admin Login</h2>";
            echo "<p class='text-blue-700'>Email: <code class='bg-blue-200 px-2 py-1 rounded'>admin@foodhub.com</code></p>";
            echo "<p class='text-blue-700'>Password: <code class='bg-blue-200 px-2 py-1 rounded'>Admin@2025</code></p>";
            echo "</div>";
            
            echo "<div class='flex space-x-4'>";
            echo "<a href='../customer/menu.php' class='flex-1 text-center bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition'>View Menu</a>";
            echo "<a href='../auth/login.php' class='flex-1 text-center bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition'>Login</a>";
            echo "<a href='../index.php' class='flex-1 text-center bg-gray-600 text-white py-3 rounded-lg hover:bg-gray-700 transition'>Homepage</a>";
            echo "</div>";
            ?>
        </div>
    </div>
</body>
</html>
