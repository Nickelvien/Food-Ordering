-- Food Ordering System Database Schema
-- Created: October 2025
-- Database: food_ordering_system

-- Create Database
CREATE DATABASE IF NOT EXISTS food_ordering_system;
USE food_ordering_system;

-- Users Table (for authentication and role management)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories Table (for food categories)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Food Items Table
CREATE TABLE IF NOT EXISTS food_items (
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
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
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
);

-- Order Items Table (items in each order)
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    food_item_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (food_item_id) REFERENCES food_items(id) ON DELETE CASCADE
);

-- Messages Table (for contact form)
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin User
-- Password: admin123 (hashed)
INSERT INTO users (full_name, email, password, role) VALUES
('Admin User', 'admin@foodorder.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert Sample Categories
INSERT INTO categories (name, description, image, is_active) VALUES
('Burgers', 'Delicious burgers made with fresh ingredients', 'burger-category.jpg', 1),
('Pasta', 'Italian pasta dishes with authentic flavors', 'pasta-category.jpg', 1),
('Drinks', 'Refreshing beverages and smoothies', 'drinks-category.jpg', 1),
('Desserts', 'Sweet treats and delightful desserts', 'dessert-category.jpg', 1),
('Pizza', 'Wood-fired pizzas with premium toppings', 'pizza-category.jpg', 1),
('Salads', 'Fresh and healthy salad options', 'salad-category.jpg', 1);

-- Insert Sample Food Items
INSERT INTO food_items (category_id, name, description, price, image, is_available, is_featured) VALUES
-- Burgers
(1, 'Classic Beef Burger', 'Juicy beef patty with lettuce, tomato, and special sauce', 8.99, 'beef-burger.jpg', 1, 1),
(1, 'Chicken Burger', 'Grilled chicken breast with mayo and fresh veggies', 7.99, 'chicken-burger.jpg', 1, 1),
(1, 'Veggie Burger', 'Plant-based patty with avocado and special sauce', 7.49, 'veggie-burger.jpg', 1, 0),
(1, 'Double Cheese Burger', 'Double beef patties with melted cheese', 10.99, 'double-burger.jpg', 1, 1),

-- Pasta
(2, 'Spaghetti Carbonara', 'Creamy pasta with bacon and parmesan', 12.99, 'carbonara.jpg', 1, 1),
(2, 'Penne Arrabbiata', 'Spicy tomato sauce with garlic and chili', 10.99, 'arrabbiata.jpg', 1, 0),
(2, 'Fettuccine Alfredo', 'Rich and creamy alfredo sauce', 11.99, 'alfredo.jpg', 1, 1),
(2, 'Lasagna', 'Layered pasta with meat sauce and cheese', 13.99, 'lasagna.jpg', 1, 0),

-- Drinks
(3, 'Fresh Orange Juice', 'Freshly squeezed orange juice', 4.99, 'orange-juice.jpg', 1, 0),
(3, 'Mango Smoothie', 'Tropical mango smoothie with yogurt', 5.99, 'mango-smoothie.jpg', 1, 1),
(3, 'Iced Coffee', 'Cold brew coffee with ice', 3.99, 'iced-coffee.jpg', 1, 0),
(3, 'Lemonade', 'Refreshing homemade lemonade', 3.49, 'lemonade.jpg', 1, 0),

-- Desserts
(4, 'Chocolate Cake', 'Rich chocolate cake with ganache', 6.99, 'chocolate-cake.jpg', 1, 1),
(4, 'Tiramisu', 'Classic Italian coffee-flavored dessert', 7.99, 'tiramisu.jpg', 1, 1),
(4, 'Ice Cream Sundae', 'Vanilla ice cream with toppings', 5.99, 'sundae.jpg', 1, 0),
(4, 'Cheesecake', 'New York style cheesecake', 7.49, 'cheesecake.jpg', 1, 0),

-- Pizza
(5, 'Margherita Pizza', 'Classic tomato, mozzarella, and basil', 11.99, 'margherita.jpg', 1, 1),
(5, 'Pepperoni Pizza', 'Loaded with pepperoni and cheese', 13.99, 'pepperoni.jpg', 1, 1),
(5, 'Vegetarian Pizza', 'Fresh vegetables and cheese', 12.49, 'veggie-pizza.jpg', 1, 0),

-- Salads
(6, 'Caesar Salad', 'Romaine lettuce with caesar dressing', 8.99, 'caesar-salad.jpg', 1, 0),
(6, 'Greek Salad', 'Fresh vegetables with feta cheese', 9.49, 'greek-salad.jpg', 1, 1);

-- Insert Sample Customer User
-- Password: customer123 (hashed)
INSERT INTO users (full_name, email, password, phone, address, role) VALUES
('John Doe', 'customer@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1234567890', '123 Main Street, City, State', 'customer');
