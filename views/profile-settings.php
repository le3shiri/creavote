<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';
$user_id = $_SESSION['user_id'];
// Fetch user info
global $pdo;
$stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// Fetch payment cards (mock if table/cards do not exist)
$cards = [];
try {
    $card_stmt = $pdo->prepare('SELECT * FROM cards WHERE user_id = ?');
    $card_stmt->execute([$user_id]);
    $cards = $card_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // If no cards table, leave $cards empty
}
function render_main() {
    global $user, $cards;
?>
<div class="flex-1 flex flex-col px-8 py-8 bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto">
        <a href="profile.php" class="flex items-center mb-6 text-gray-600 hover:text-purple-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Settings
        </a>
        <!-- User Card -->
        <div class="flex items-center bg-white rounded-xl shadow p-6 mb-8">
            <img src="<?php echo htmlspecialchars($user['profile_picture'] ?? '/assets/default-profile.png'); ?>" class="w-20 h-20 rounded-full border-4 border-white shadow mr-6" alt="User">
            <div class="flex-1">
                <div class="text-2xl font-bold"><?php echo htmlspecialchars(($user['firstname'] ?? '') . '_' . ($user['lastname'] ?? '')); ?></div>
                <div class="text-gray-500"><?php echo htmlspecialchars(ucfirst($user['role'] ?? '')); ?></div>
            </div>
            <a href="profile-edit.php" class="bg-blue-100 hover:bg-blue-200 text-blue-600 px-6 py-2 rounded font-semibold">Edit profile</a>
        </div>
        <!-- Account Security -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <?php if (!empty($_SESSION['password_change_errors'])): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php foreach ($_SESSION['password_change_errors'] as $err): ?>
                        <div><?php echo htmlspecialchars($err); ?></div>
                    <?php endforeach; ?>
                </div>
                <?php unset($_SESSION['password_change_errors']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['password_change_success'])): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($_SESSION['password_change_success']); ?>
                </div>
                <?php unset($_SESSION['password_change_success']); ?>
            <?php endif; ?>

            <h2 class="text-lg font-bold mb-4">Account Security</h2>
            <form action="../controllers/profile/update-password.php" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                    <div>
                        <label class="block text-gray-700 mb-1">Current Password</label>
                        <input type="password" name="current_password" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500" oninput="checkStrength(this.value)">
                    </div>
                </div>
                <div class="flex items-center mb-2">
                    <div class="w-32 mr-2 text-xs text-gray-500">Password strength:</div>
                    <div class="flex-1">
                        <div class="w-full h-2 bg-gray-200 rounded">
                            <div id="strength-bar" class="h-2 bg-yellow-400 rounded" style="width: 20%;"></div>
                        </div>
                    </div>
                </div>
                <div class="text-xs text-gray-400 mb-4">Use at least 8 characters, including letters, numbers, and symbols</div>
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded font-semibold">Update Password</button>
            </form>
        </div>
        <!-- Payments Methods -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold">Payments Methods</h2>
                <a href="add-card.php" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded font-semibold">Add card</a>
            </div>
            <?php if (!empty($cards)): ?>
<form action="../controllers/payment/set-default-card.php" method="POST">
    <div class="space-y-4">
        <?php foreach ($cards as $card): ?>
            <label class="flex items-center gap-4 p-3 border rounded cursor-pointer">
                <input type="radio" name="default_card" value="<?php echo htmlspecialchars($card['card_id']); ?>" class="accent-blue-500" <?php if (!empty($card['is_default'])) echo 'checked'; ?>>
                <img src="<?php echo htmlspecialchars($card['brand_icon'] ?? 'https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg'); ?>" alt="<?php echo htmlspecialchars($card['brand'] ?? 'Card'); ?>" class="w-10 h-6 object-contain">
                <div class="flex-1">
                    <div class="font-semibold"><?php echo htmlspecialchars($card['brand'] ?? 'Card'); ?> •••• <?php echo htmlspecialchars(substr($card['last4'] ?? '0000', -4)); ?></div>
                    <div class="text-xs text-gray-500">Expires <?php echo htmlspecialchars($card['exp_month'] ?? '--'); ?>/<?php echo htmlspecialchars($card['exp_year'] ?? '--'); ?></div>
                </div>
            </label>
        <?php endforeach; ?>
    </div>
</form>
<?php else: ?>
    <div class="text-gray-400 text-center py-4">No cards found.</div>
    <a href="add-card.php" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded font-semibold">Add card</a>
<?php endif; ?>
        </div>
    </div>
</div>
<script>
function checkStrength(pw) {
    let bar = document.getElementById('strength-bar');
    let strength = 0;
    if (pw.length >= 8) strength += 1;
    if (/[A-Z]/.test(pw)) strength += 1;
    if (/[0-9]/.test(pw)) strength += 1;
    if (/[^A-Za-z0-9]/.test(pw)) strength += 1;
    let width = ['20%', '40%', '70%', '100%'][strength] || '20%';
    let color = ['bg-yellow-400', 'bg-yellow-400', 'bg-blue-400', 'bg-green-500'][strength] || 'bg-yellow-400';
    bar.style.width = width;
    bar.className = 'h-2 rounded ' + color;
}
</script>
<?php
}
include 'base.php';
