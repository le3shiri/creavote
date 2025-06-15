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
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
<div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r flex flex-col justify-between py-6 px-4 fixed top-0 left-0 bottom-0 z-30 h-screen">

        <div>
            <div class="flex items-center mb-10">
                <span class="text-3xl font-extrabold text-purple-600 mr-2">CREAVOTE</span>
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
                <a href="home.php" class="flex items-center px-4 py-2 rounded-lg text-purple-600 bg-purple-100 font-semibold"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6m-6 0H7m6 0v6m0 0H7m6 0h6" /></svg>Home</a>
                <a href="offers.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-purple-50"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2a4 4 0 004 4h2a4 4 0 004-4z" /></svg>Offers</a>
                <a href="videos.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-purple-50"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A2 2 0 0021 6.382V17.618a2 2 0 01-1.447 1.894L15 17.618M15 10v7.618m0-7.618L9 13.618m6-3.618v7.618m0-7.618L3 17.618m6-3.618v7.618" /></svg>Videos</a>
                <?php if (!empty($_SESSION['user_id'])): ?>
                <a href="notifications.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-purple-50 relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    Notifications
                    <?php if ($unread_count > 0): ?>
                        <span class="absolute top-1 right-1 bg-red-500 text-white text-xs rounded-full px-2 py-0.5 font-bold animate-pulse"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                <a href="profile.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-purple-50"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A8.966 8.966 0 0112 15c2.21 0 4.243.805 5.879 2.146M15 10a3 3 0 11-6 0 3 3 0 016 0z" /></svg>Profile</a>
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
