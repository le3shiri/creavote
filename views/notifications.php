<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
// Mark all notifications as read for this user immediately on page load
$pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0')->execute([$user_id]);
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
                            <?php
                            $is_offer = $notif['type'] === 'message' && strpos($notif['message'], 'A new offer') !== false;
                            if ($is_offer && preg_match('/offer \"(.+?)\"/', $notif['message'], $m)) {
                                // Find offer title, then fetch its id from DB
                                $offer_title = $m[1];
                                // This is a quick DB fetch for the offer_id by title
                                require __DIR__ . '/../config/db.php';
                                $offer_stmt = $pdo->prepare('SELECT offer_id FROM offers WHERE offer_title = ? LIMIT 1');
                                $offer_stmt->execute([$offer_title]);
                                $offer_row = $offer_stmt->fetch(PDO::FETCH_ASSOC);
                                $offer_id = $offer_row ? $offer_row['offer_id'] : null;
                            }
                            ?>
                            <?php if ($is_offer && !empty($offer_id)): ?>
                                <a href="offer-details.php?id=<?php echo urlencode($offer_id); ?>" class="text-blue-600 hover:underline notif-link" data-id="<?php echo $notif['notification_id']; ?>">
                                    <?php echo htmlspecialchars($notif['message']); ?>
                                </a>
                            <?php else: ?>
                                <span><?php echo htmlspecialchars($notif['message']); ?></span>
                            <?php endif; ?>
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
<script>
// Mark notification as read on click, then redirect
function markNotifReadAndGo(url, notifId) {
    fetch('/creavote/controllers/notifications.php?action=mark_read&id=' + notifId)
      .then(() => { window.location = url; });
}
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.notif-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            markNotifReadAndGo(this.getAttribute('href'), this.dataset.id);
        });
    });
    // Badge for unread count (simple placeholder, should be moved to bell in navbar if exists)
    fetch('/creavote/controllers/notifications.php?action=count_unread')
      .then(res => res.json())
      .then(data => {
        if (data.success && data.count > 0) {
            let badge = document.createElement('span');
            badge.textContent = data.count;
            badge.className = 'inline-block ml-2 px-2 py-1 bg-red-500 text-white text-xs rounded-full';
            document.querySelector('h1').appendChild(badge);
        }
      });
});
</script>
<?php }
include 'base.php';
