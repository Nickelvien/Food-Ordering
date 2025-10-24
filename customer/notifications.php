<?php
session_start();
require_once '../db.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('../auth/login.php');
}

$user_id = $_SESSION['user_id'];

// Mark notification as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    $id = (int)$_POST['id'];
    mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE id = $id AND user_id = $user_id");
    header('Location: notifications.php');
    exit;
}

// Mark all as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_all_read'])) {
    mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE user_role = 'user' AND user_id = $user_id AND is_read = 0");
    header('Location: notifications.php');
    exit;
}

// Get user notifications
$notifs = mysqli_query($conn, "SELECT * FROM notifications WHERE user_role='user' AND user_id = $user_id ORDER BY created_at DESC LIMIT 50");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notifications - Gourmet Sentinel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .notif-toast {
            position: fixed;
            right: 20px;
            bottom: 90px;
            background: white;
            color: #111;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
            z-index: 9999;
            max-width: 350px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .notif-item.unread {
            background: #fff7ed;
            border-left: 4px solid #f59e0b;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-utensils text-3xl text-primary"></i>
                    <span class="text-2xl font-bold text-gray-800">Gourmet Sentinel</span>
                </div>
                
                <div class="flex space-x-6">
                    <a href="../index.php" class="text-gray-700 hover:text-primary">Home</a>
                    <a href="menu.php" class="text-gray-700 hover:text-primary">Menu</a>
                    <a href="orders.php" class="text-gray-700 hover:text-primary">My Orders</a>
                    <a href="../logout.php" class="text-gray-700 hover:text-primary">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-bell text-primary"></i> My Notifications
            </h1>
            <?php if (mysqli_num_rows($notifs) > 0): ?>
            <form method="post">
                <button type="submit" name="mark_all_read" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    <i class="fas fa-check-double"></i> Mark All as Read
                </button>
            </form>
            <?php endif; ?>
        </div>

        <div class="space-y-4">
            <?php if (mysqli_num_rows($notifs) > 0): ?>
                <?php while ($n = mysqli_fetch_assoc($notifs)): ?>
                <div class="notif-item <?php echo $n['is_read'] ? '' : 'unread'; ?> bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-bold text-lg text-gray-800">
                            <?php if (!$n['is_read']): ?>
                                <i class="fas fa-circle text-xs text-primary"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($n['title']); ?>
                        </h3>
                        <span class="text-sm text-gray-500">
                            <i class="far fa-clock"></i> <?php echo date('M d, h:i A', strtotime($n['created_at'])); ?>
                        </span>
                    </div>
                    <p class="text-gray-600 mb-4 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($n['message'])); ?></p>
                    <div class="flex gap-3">
                        <?php if (!$n['is_read']): ?>
                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo $n['id']; ?>">
                            <button type="submit" name="mark_read" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                <i class="fas fa-check"></i> Mark as Read
                            </button>
                        </form>
                        <?php endif; ?>
                        <?php if ($n['related_id']): ?>
                        <a href="orders.php" class="bg-primary text-white px-4 py-2 rounded hover:bg-secondary">
                            <i class="fas fa-receipt"></i> View Order
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
                    <p class="text-xl text-gray-500">No notifications yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f59e0b',
                        secondary: '#fb923c',
                    }
                }
            }
        }
    </script>
</body>
</html>
