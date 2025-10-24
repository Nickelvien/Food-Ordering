<?php
require_once '../db.php';

// New admin credentials
$username = 'Admin';
$email = 'admin@foodhub.com';
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Update or create admin user
$check_query = "SELECT id FROM users WHERE role = 'admin' LIMIT 1";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    // Update existing admin
    $update_query = "UPDATE users SET 
                     full_name = '$username',
                     email = '$email',
                     password = '$hashed_password'
                     WHERE role = 'admin' LIMIT 1";
    
    if (mysqli_query($conn, $update_query)) {
        $message = "✅ Admin credentials updated successfully!";
        $status = "success";
    } else {
        $message = "❌ Error updating admin: " . mysqli_error($conn);
        $status = "error";
    }
} else {
    // Create new admin
    $insert_query = "INSERT INTO users (full_name, email, password, phone, address, role) 
                     VALUES ('$username', '$email', '$hashed_password', '+1234567890', 'Admin Address', 'admin')";
    
    if (mysqli_query($conn, $insert_query)) {
        $message = "✅ Admin user created successfully!";
        $status = "success";
    } else {
        $message = "❌ Error creating admin: " . mysqli_error($conn);
        $status = "error";
    }
}

// Get admin info to verify
$verify_query = "SELECT id, full_name, email, role FROM users WHERE role = 'admin' LIMIT 1";
$verify_result = mysqli_query($conn, $verify_query);
$admin = mysqli_fetch_assoc($verify_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Credentials Updated - FoodHub</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #f59e0b; text-align: center; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #dc3545; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #2196f3; }
        .credentials { background: #fff3cd; padding: 20px; border-radius: 5px; margin: 20px 0; border: 2px solid #ffc107; }
        .credentials h3 { margin-top: 0; color: #856404; }
        .credential-item { margin: 10px 0; font-size: 18px; }
        .credential-label { font-weight: bold; color: #555; }
        .credential-value { color: #f59e0b; font-family: monospace; background: #f9f9f9; padding: 5px 10px; border-radius: 3px; }
        .btn { display: inline-block; padding: 12px 24px; margin: 10px 5px; text-decoration: none; border-radius: 5px; font-weight: bold; cursor: pointer; text-align: center; }
        .btn-primary { background: #f59e0b; color: white; }
        .btn-primary:hover { background: #fb923c; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        .button-group { text-align: center; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Admin Credentials Updated</h1>
        
        <?php if ($status == "success"): ?>
            <div class="success">
                <?php echo $message; ?>
            </div>
            
            <div class="credentials">
                <h3>🎯 New Admin Login Credentials:</h3>
                <div class="credential-item">
                    <span class="credential-label">Username/Email:</span>
                    <span class="credential-value"><?php echo $admin['email']; ?></span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Password:</span>
                    <span class="credential-value">admin123</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Display Name:</span>
                    <span class="credential-value"><?php echo $admin['full_name']; ?></span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Role:</span>
                    <span class="credential-value"><?php echo $admin['role']; ?></span>
                </div>
            </div>
            
            <div class="info">
                <h4>✅ Additional Updates Made:</h4>
                <ul>
                    <li>✅ Login form now has <strong>show/hide password toggle</strong></li>
                    <li>✅ Sign up form has <strong>address field removed</strong></li>
                    <li>✅ Admin credentials updated to new values</li>
                </ul>
            </div>
            
            <div class="button-group">
                <a href="../auth/login.php" class="btn btn-primary">🔑 Go to Login Page</a>
                <a href="../admin/dashboard.php" class="btn btn-success">📊 Admin Dashboard</a>
            </div>
            
        <?php else: ?>
            <div class="error">
                <?php echo $message; ?>
            </div>
            <div class="button-group">
                <a href="javascript:location.reload()" class="btn btn-primary">🔄 Try Again</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
