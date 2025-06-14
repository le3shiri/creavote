
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Creavote</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-[#f5f8ff]">
    <div class="flex flex-col md:flex-row items-center gap-10 w-full max-w-4xl mx-auto p-4">
        <!-- Illustration -->
        <div class="flex-1 flex items-center justify-center">
            <img src="https://cdn.pixabay.com/photo/2023/10/05/10/54/megaphone-8293979_1280.png" alt="Signup Illustration" class="max-h-96 w-auto">
        </div>
        <!-- Signup Form -->
        <div class="flex-1 bg-white rounded-[2.5rem] shadow-2xl p-10 max-w-md w-full">
            <?php
            if (session_status() === PHP_SESSION_NONE) session_start();
            if (!empty($_SESSION['signup_errors'])) {
                echo '<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">';
                foreach ($_SESSION['signup_errors'] as $error) {
                    echo '<div>'.htmlspecialchars($error).'</div>';
                }
                echo '</div>';
                unset($_SESSION['signup_errors']);
            }
            ?>
            <form action="../controllers/auth/signup.php" method="POST" class="space-y-5">
                <div class="flex gap-3">
                    <div class="flex-1">
                        <label class="block text-gray-700 mb-1 text-sm">First Name</label>
                        <input type="text" name="first_name" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8b5cf6] placeholder-gray-400" placeholder="first name">
                    </div>
                    <div class="flex-1">
                        <label class="block text-gray-700 mb-1 text-sm">Last Name</label>
                        <input type="text" name="last_name" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8b5cf6] placeholder-gray-400" placeholder="last name">
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1 text-sm">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8b5cf6] placeholder-gray-400" placeholder="your email">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1 text-sm">Phone Number</label>
                    <div class="flex">
                        <span class="flex items-center px-3 bg-gray-100 border border-r-0 rounded-l-xl text-gray-600"><img src="https://flagcdn.com/24x18/ma.png" class="w-6 h-4 mr-1">+212</span>
                        <input type="text" name="phone" required class="w-full px-4 py-3 border border-gray-200 border-l-0 rounded-r-xl focus:outline-none focus:ring-2 focus:ring-[#8b5cf6] placeholder-gray-400" placeholder="">
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="flex-1">
                        <label class="block text-gray-700 mb-1 text-sm">Password</label>
                        <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8b5cf6] placeholder-gray-400" placeholder="your password">
                    </div>
                    <div class="flex-1">
                        <label class="block text-gray-700 mb-1 text-sm">Confirm Password</label>
                        <input type="password" name="confirm_password" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8b5cf6] placeholder-gray-400" placeholder="Confirm Password">
                    </div>
                </div>
                <button type="submit" class="w-full bg-[#3b82f6] hover:bg-[#2563eb] text-white px-6 py-3 rounded-xl font-bold text-lg shadow mt-2 transition">Sign in</button>
            </form>
            <div class="flex items-center my-6">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="mx-3 text-gray-400">Or sign up with</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>
            <div class="flex justify-center gap-5 mb-5">
                <button class="bg-white border border-gray-200 rounded-full p-3 shadow hover:bg-gray-100 transition"><img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" alt="Google" class="w-7 h-7"></button>
                <button class="bg-white border border-gray-200 rounded-full p-3 shadow hover:bg-gray-100 transition"><img src="https://upload.wikimedia.org/wikipedia/commons/0/05/Facebook_Logo_%282019%29.png" alt="Facebook" class="w-7 h-7"></button>
                <button class="bg-white border border-gray-200 rounded-full p-3 shadow hover:bg-gray-100 transition"><img src="https://upload.wikimedia.org/wikipedia/commons/9/91/Octicons-mark-github.svg" alt="GitHub" class="w-7 h-7"></button>
            </div>
            <div class="text-center text-gray-500 text-base">
                Already have an account? <a href="login.php" class="text-[#3b82f6] font-semibold hover:underline">Log in</a>
            </div>
        </div>
    </div>
</body>
</html>
