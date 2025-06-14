<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creavote - Login</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Roboto Font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
      body { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="w-full max-w-md bg-white shadow-lg rounded-xl p-8">
        <div class="flex items-center justify-center mb-6">
            <span class="text-3xl font-bold text-purple-600">Creavote</span>
        </div>
        <?php
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!empty($_SESSION['login_errors'])) {
            echo '<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">';
            foreach ($_SESSION['login_errors'] as $error) {
                echo '<div>'.htmlspecialchars($error).'</div>';
            }
            echo '</div>';
            unset($_SESSION['login_errors']);
        }
        ?>
        <form action="../controllers/auth/login.php" method="POST" class="space-y-5">
            <div>
                <label for="email" class="block text-gray-700">Email or Username</label>
                <input type="text" id="email" name="email" required class="mt-1 w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" id="password" name="password" required class="mt-1 w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <input type="checkbox" id="remember" name="remember" class="mr-1">
                    <label for="remember" class="text-sm text-gray-600">Remember Me</label>
                </div>
                <a href="forgot-password.php" class="text-sm text-purple-600 hover:underline">Forgot Password?</a>
            </div>
            <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700 font-semibold">Login</button>
        </form>
        <div class="mt-6 text-center">
            <span class="text-gray-600">Don't have an account?</span>
            <a href="signup.php" class="text-purple-600 hover:underline font-semibold">Sign Up</a>
        </div>
    </div>
</body>
</html>
