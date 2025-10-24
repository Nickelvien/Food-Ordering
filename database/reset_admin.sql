-- Script to Reset Users and Create New Admin Account
-- Database: food_ordering_system
-- Created: October 8, 2025

USE food_ordering_system;

-- Step 1: Delete all existing users
DELETE FROM users;

-- Step 2: Reset auto-increment counter
ALTER TABLE users AUTO_INCREMENT = 1;

-- Step 3: Create new admin account
-- Email: admin@foodhub.com
-- Password: Admin@2025
-- Password is hashed using PHP password_hash() with bcrypt
INSERT INTO users (full_name, email, password, phone, address, role, created_at) VALUES
('System Administrator', 'admin@foodhub.com', '$2y$10$vQ5qF7xK8mHw9Y.rNx3zTOYGZJ8YH6XjF5B7kQtL9wX2Rp3VmN4Uy', '+1-555-ADMIN', '123 Admin Street, Admin City', 'admin', NOW());

-- Verify the new admin user was created
SELECT id, full_name, email, role, created_at FROM users WHERE role = 'admin';

-- Display success message
SELECT 'Admin user created successfully!' AS Status,
       'Email: admin@foodhub.com' AS Email,
       'Password: Admin@2025' AS Password,
       'Please save these credentials!' AS Note;
