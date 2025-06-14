<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
// Fetch notifications for this user
$stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
function render_main() {
    global $notifications;
?>
<div class="flex-1 flex flex-col px-8 py-8 bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Notifications</h1>
        <?php if ($notifications): ?>
            <ul class="space-y-4">
                <?php foreach ($notifications as $notif): ?>
                    <li class="bg-white rounded shadow p-4 flex items-center <?php echo $notif['is_read'] ? 'opacity-60' : ''; ?>">
                        <?php if ($notif['type'] === 'saved'): ?>
                            <span class="inline-block bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-semibold mr-4">Saved</span>
                        <?php elseif ($notif['type'] === 'vote'): ?>
                            <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold mr-4">Vote</span>
                        <?php elseif ($notif['type'] === 'message'): ?>
                            <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold mr-4">Message</span>
                        <?php endif; ?>
                        <div class="flex-1">
                            <div class="text-gray-800"><?php echo htmlspecialchars($notif['message']); ?></div>
                            <div class="text-xs text-gray-400 mt-1"><?php echo date('M d, Y H:i', strtotime($notif['created_at'])); ?></div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="text-gray-400 text-center py-12">No notifications yet.</div>
        <?php endif; ?>
    </div>
</div>
<?php }
include 'base.php';
