<?php
/**
 * Reset Users and Create New Admin Account
 * This script will delete all users and create a fresh admin account
 */

// Include database connection
require_once '../db.php';

// Admin account details
$admin_email = 'admin@foodhub.com';
$admin_password = 'Admin@2025';  // Change this to your desired password
$admin_full_name = 'System Administrator';
$admin_phone = '+1-555-ADMIN';
$admin_address = '123 Admin Street, Admin City';

// Hash the password securely
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Reset Admin Account</title>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body class='bg-gray-100'>
    <div class='container mx-auto px-4 py-8'>
        <div class='max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8'>";

// Step 1: Delete all existing users
echo "<h1 class='text-3xl font-bold text-gray-800 mb-6'>User Reset Process</h1>";
echo "<div class='space-y-4'>";

// Check if there are existing users
$check_query = "SELECT COUNT(*) as total FROM users";
$check_result = mysqli_query($conn, $check_query);
$user_count = mysqli_fetch_assoc($check_result)['total'];

echo "<div class='bg-blue-50 border-l-4 border-blue-500 p-4'>
        <p class='font-semibold text-blue-800'>Step 1: Checking existing users...</p>
        <p class='text-blue-700'>Found: <strong>{$user_count}</strong> user(s) in database</p>
      </div>";

// Delete all users
$delete_query = "DELETE FROM users";
if (mysqli_query($conn, $delete_query)) {
    echo "<div class='bg-green-50 border-l-4 border-green-500 p-4'>
            <p class='font-semibold text-green-800'>Step 2: Users deleted successfully!</p>
            <p class='text-green-700'>All {$user_count} user(s) have been removed from the database.</p>
          </div>";
} else {
    echo "<div class='bg-red-50 border-l-4 border-red-500 p-4'>
            <p class='font-semibold text-red-800'>Error deleting users:</p>
            <p class='text-red-700'>" . mysqli_error($conn) . "</p>
          </div>";
    exit;
}

// Reset auto-increment
$reset_query = "ALTER TABLE users AUTO_INCREMENT = 1";
mysqli_query($conn, $reset_query);

echo "<div class='bg-blue-50 border-l-4 border-blue-500 p-4'>
        <p class='font-semibold text-blue-800'>Step 3: Auto-increment counter reset</p>
        <p class='text-blue-700'>User ID counter has been reset to 1.</p>
      </div>";

// Step 2: Create new admin account
$insert_query = "INSERT INTO users (full_name, email, password, phone, address, role, created_at) 
                 VALUES (?, ?, ?, ?, ?, 'admin', NOW())";

$stmt = mysqli_prepare($conn, $insert_query);
mysqli_stmt_bind_param($stmt, "sssss", $admin_full_name, $admin_email, $hashed_password, $admin_phone, $admin_address);

if (mysqli_stmt_execute($stmt)) {
    $new_admin_id = mysqli_insert_id($conn);
    
    echo "<div class='bg-green-50 border-l-4 border-green-500 p-4'>
            <p class='font-semibold text-green-800 text-xl'>✓ Admin Account Created Successfully!</p>
            <p class='text-green-700 mt-2'>User ID: <strong>{$new_admin_id}</strong></p>
          </div>";
    
    // Display credentials
    echo "<div class='bg-yellow-50 border-l-4 border-yellow-500 p-6 mt-6'>
            <h2 class='font-bold text-yellow-800 text-xl mb-4'>🔐 New Admin Credentials</h2>
            <div class='space-y-2 text-yellow-900'>
                <p><strong>Email:</strong> <code class='bg-yellow-200 px-2 py-1 rounded'>{$admin_email}</code></p>
                <p><strong>Password:</strong> <code class='bg-yellow-200 px-2 py-1 rounded'>{$admin_password}</code></p>
                <p><strong>Full Name:</strong> {$admin_full_name}</p>
                <p><strong>Role:</strong> Administrator</p>
            </div>
            <div class='mt-4 p-3 bg-yellow-100 rounded'>
                <p class='font-semibold text-yellow-800'>⚠️ Important:</p>
                <ul class='list-disc list-inside text-yellow-700 text-sm mt-2'>
                    <li>Save these credentials in a secure location</li>
                    <li>Delete this file after use for security</li>
                    <li>Change your password after first login</li>
                </ul>
            </div>
          </div>";
    
    // Display verification
    $verify_query = "SELECT id, full_name, email, role, created_at FROM users WHERE id = ?";
    $verify_stmt = mysqli_prepare($conn, $verify_query);
    mysqli_stmt_bind_param($verify_stmt, "i", $new_admin_id);
    mysqli_stmt_execute($verify_stmt);
    $verify_result = mysqli_stmt_get_result($verify_stmt);
    $admin_data = mysqli_fetch_assoc($verify_result);
    
    echo "<div class='bg-gray-50 border border-gray-300 rounded p-4 mt-6'>
            <h3 class='font-bold text-gray-800 mb-3'>Database Verification:</h3>
            <pre class='bg-gray-800 text-green-400 p-4 rounded overflow-x-auto text-sm'>";
    print_r($admin_data);
    echo "</pre>
          </div>";
    
} else {
    echo "<div class='bg-red-50 border-l-4 border-red-500 p-4'>
            <p class='font-semibold text-red-800'>Error creating admin account:</p>
            <p class='text-red-700'>" . mysqli_error($conn) . "</p>
          </div>";
}

// Action buttons
echo "<div class='mt-8 flex space-x-4'>
        <a href='../auth/login.php' class='bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition'>
            Login as Admin
        </a>
        <a href='../admin/dashboard.php' class='bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition'>
            Go to Admin Dashboard
        </a>
        <a href='../index.php' class='bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition'>
            Go to Homepage
        </a>
      </div>";

echo "<div class='mt-8 p-4 bg-red-50 border border-red-300 rounded'>
        <p class='font-bold text-red-800'>🔒 Security Notice:</p>
        <p class='text-red-700 mt-2'>For security reasons, please delete this file after use:</p>
        <code class='block bg-red-100 px-3 py-2 rounded mt-2 text-sm'>database/reset_admin.php</code>
      </div>";

echo "</div>
    </div>
</body>
</html>";

mysqli_close($conn);
?>
