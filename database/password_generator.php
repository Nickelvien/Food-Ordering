<?php
/**
 * Password Hash Generator
 * Use this tool to generate secure password hashes for manual database insertion
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hash Generator - FoodHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">
                <i class="fas fa-key text-yellow-500"></i> Password Hash Generator
            </h1>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
                $password = $_POST['password'];
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                
                echo "<div class='bg-green-50 border-l-4 border-green-500 p-6 mb-6'>
                        <h2 class='font-bold text-green-800 text-xl mb-4'>✓ Password Hashed Successfully!</h2>
                        <div class='space-y-4'>
                            <div>
                                <label class='block text-sm font-semibold text-gray-700 mb-2'>Original Password:</label>
                                <code class='block bg-gray-800 text-yellow-400 p-3 rounded break-all'>" . htmlspecialchars($password) . "</code>
                            </div>
                            <div>
                                <label class='block text-sm font-semibold text-gray-700 mb-2'>Hashed Password (for database):</label>
                                <code class='block bg-gray-800 text-green-400 p-3 rounded break-all'>" . htmlspecialchars($hashed) . "</code>
                            </div>
                        </div>
                        <div class='mt-4 p-3 bg-blue-50 rounded'>
                            <p class='text-sm text-blue-800'><strong>How to use:</strong> Copy the hashed password above and use it in your SQL INSERT statement.</p>
                        </div>
                      </div>";
                
                echo "<div class='bg-gray-50 border border-gray-300 rounded p-4 mb-6'>
                        <h3 class='font-bold text-gray-800 mb-3'>SQL Example:</h3>
                        <pre class='bg-gray-800 text-white p-4 rounded overflow-x-auto text-sm'>INSERT INTO users (full_name, email, password, role) VALUES
('User Name', 'user@example.com', '" . htmlspecialchars($hashed) . "', 'admin');</pre>
                      </div>";
            }
            ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Enter Password to Hash:</label>
                    <input type="text" 
                           name="password" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="Enter your password here"
                           required>
                    <p class="text-sm text-gray-500 mt-2">⚠️ This will display your password in plain text. Use only for development.</p>
                </div>

                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Generate Hash
                </button>
            </form>

            <div class="mt-8 p-4 bg-yellow-50 border-l-4 border-yellow-500">
                <h3 class="font-bold text-yellow-800 mb-2">📝 About Password Hashing</h3>
                <ul class="list-disc list-inside text-yellow-700 text-sm space-y-1">
                    <li>Uses PHP's <code>password_hash()</code> with bcrypt algorithm</li>
                    <li>Each hash is unique even for the same password</li>
                    <li>Hashed passwords cannot be reversed to original</li>
                    <li>Recommended for all password storage in database</li>
                </ul>
            </div>

            <div class="mt-6 flex space-x-4">
                <a href="../index.php" class="flex-1 text-center bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700 transition">
                    ← Back to Home
                </a>
                <a href="reset_admin.php" class="flex-1 text-center bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                    Reset Admin User
                </a>
            </div>

            <div class="mt-6 p-4 bg-red-50 border border-red-300 rounded">
                <p class="font-bold text-red-800">🔒 Security Notice:</p>
                <p class="text-red-700 text-sm mt-1">Delete this file in production environments. Only use for development purposes.</p>
            </div>
        </div>
    </div>
</body>
</html>
