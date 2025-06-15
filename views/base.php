<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creavote</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Roboto Font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
      body { font-family: 'Roboto', sans-serif; }
      /* Custom Blue Theme */
      .btn-primary {
        background-color: #55A9FF;
        color: white;
        transition: all 0.2s;
      }
      .btn-primary:hover {
        background-color: #3d94f5;
        transform: translateY(-1px);
      }
      .text-primary { color: #55A9FF; }
      .bg-primary { background-color: #55A9FF; }
      .border-primary { border-color: #55A9FF; }
      .hover\:bg-primary:hover { background-color: #55A9FF; }
      .hover\:text-primary:hover { color: #55A9FF; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
<div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r flex flex-col justify-between py-6 px-4 fixed top-0 left-0 bottom-0 z-30 h-screen">

        <div>
            <div class="flex items-center mb-10">
                <img src="../assets/logo.jpg" alt="Logo" class="w-20 h-20">
                <span class="text-3xl font-extrabold text-[#55A9FF] mr-2">CREAVOTE</span>
            </div>
            <?php
            if (session_status() !== PHP_SESSION_ACTIVE) session_start();
            $unread_count = 0;
            if (!empty($_SESSION['user_id'])) {
                require_once __DIR__ . '/../config/db.php';
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
                $stmt->execute([$_SESSION['user_id']]);
                $unread_count = (int)$stmt->fetchColumn();
            }
            ?>
            <nav class="space-y-2">
                <a href="home.php" class="flex items-center px-4 py-2 rounded-lg text-white bg-[#55A9FF] font-semibold hover:bg-[#3d94f5] transition-colors"><img src="../assets/home.png" alt="">Home</a>
                <a href="offers.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-[#55A9FF11] hover:text-[#55A9FF] transition-colors"><img src="../assets/offers.png" alt="">Offers</a>
                <a href="videos.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-[#55A9FF11] hover:text-[#55A9FF] transition-colors"><img src="../assets/videos.png" alt="">Videos</a>
                <?php if (!empty($_SESSION['user_id'])): ?>
                <a href="notifications.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-[#55A9FF11] hover:text-[#55A9FF] transition-colors relative">
                    <img src="../assets/notifications.png" alt="">Notifications
                    <?php if ($unread_count > 0): ?>
                        <span class="absolute top-1 right-1 bg-red-500 text-white text-xs rounded-full px-2 py-0.5 font-bold animate-pulse"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                <a href="profile.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-[#55A9FF11] hover:text-[#55A9FF] transition-colors"><img src="../assets/profile.png" alt="">Profile</a>
            </nav>
        </div>
        <?php if (!empty($_SESSION['user_id'])): 
            // Fetch user info for sidebar
            if (!isset($sidebar_user)) {
                $stmt = $pdo->prepare('SELECT username, firstname, lastname, profile_picture FROM users WHERE user_id = ?');
                $stmt->execute([$_SESSION['user_id']]);
                $sidebar_user = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        ?>
        <div class="flex items-center space-x-3 border-t pt-4">
            <img src="<?php echo htmlspecialchars($sidebar_user['profile_picture'] ?? 'https://randomuser.me/api/portraits/lego/1.jpg'); ?>" class="w-10 h-10 rounded-full" alt="User">
            <div>
                <div class="font-semibold text-gray-800"><?php echo htmlspecialchars(trim(($sidebar_user['firstname'] ?? '').' '.($sidebar_user['lastname'] ?? '')) ?: $sidebar_user['username']); ?></div>
                <div class="text-xs text-gray-500">@<?php echo htmlspecialchars($sidebar_user['username'] ?? ''); ?></div>
            </div>
        </div>
        <?php endif; ?>
    </aside>
    <!-- Main Content -->
    <main class="flex-1 flex flex-col ml-64 mr-80 min-h-screen">
        <!-- Content injected here -->
        <?php if (function_exists('render_main')) render_main(); ?>
    </main>
    <!-- Right Panel -->
    <?php if (basename($_SERVER['SCRIPT_NAME']) !== 'videos.php'): ?>
    <aside class="w-80 bg-white border-l px-6 py-8 hidden lg:block fixed top-0 right-0 bottom-0 z-30 h-screen overflow-y-auto">
        <?php
        $total_earnings = 0;
        $recent_payments = [];
        if (!empty($_SESSION['user_id'])) {
            // Total earnings
            $stmt = $pdo->prepare('SELECT SUM(amount) FROM prizes WHERE user_id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $total_earnings = $stmt->fetchColumn() ?: 0;
            // Recent payments (join with offers for title)
            $stmt = $pdo->prepare('SELECT p.amount, o.offer_title FROM prizes p LEFT JOIN offers o ON p.offer_id = o.offer_id WHERE p.user_id = ? ORDER BY p.prize_id DESC LIMIT 3');
            $stmt->execute([$_SESSION['user_id']]);
            $recent_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        ?>
        <div class="mb-6">
            <div class="text-gray-700 font-semibold">Total Earnings</div>
            <div class="text-2xl font-bold text-green-500">$<?php echo number_format($total_earnings, 2); ?></div>
            <!-- Optionally: <div class="text-xs text-green-600 mt-1">+12% from last month</div> -->
        </div>
        <div class="mb-8">
            <canvas id="budgetChart" width="200" height="200"></canvas>
        </div>
        <div>
            <div class="text-gray-700 font-semibold mb-2">Recent Payments</div>
            <div class="space-y-2">
                <?php if ($recent_payments): ?>
                    <?php foreach ($recent_payments as $pay): ?>
                        <div class="flex justify-between items-center bg-gray-50 rounded p-2">
                            <span><?php echo htmlspecialchars($pay['offer_title'] ?: 'Untitled Offer'); ?></span>
                            <span class="text-green-500 font-semibold">+ $<?php echo number_format($pay['amount'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-gray-400 text-center py-2">No payments yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </aside>
    <?php endif; ?>
</div>
<script>
// Chart.js Line/Bar for user's earnings over time
if(document.getElementById('budgetChart')){
    fetch('earnings-data.php')
        .then(r => r.json())
        .then(res => {
            new Chart(document.getElementById('budgetChart'), {
                type: 'bar',
                data: {
                    labels: res.labels,
                    datasets: [{
                        label: 'Earnings ($)',
                        data: res.data,
                        backgroundColor: '#8b5cf6',
                        borderColor: '#7c3aed',
                        borderWidth: 2
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: v => '$'+v } }
                    }
                }
            });
        });
}
</script>
</body>
</html>
