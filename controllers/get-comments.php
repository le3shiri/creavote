<?php
require_once '../config/db.php';
session_start();
$design_id = $_GET['design_id'] ?? '';
if (!$design_id) exit('No design specified.');
$stmt = $pdo->prepare('SELECT c.*, u.username, u.profile_picture FROM comments c JOIN users u ON c.user_id = u.user_id WHERE c.design_id = ? ORDER BY c.created_at ASC');
$stmt->execute([$design_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div>
    <h2 class="text-xl font-bold mb-4">Comments</h2>
    <?php foreach ($comments as $c): ?>
        <div class="bg-gray-50 rounded-lg p-4 mb-3 shadow">
            <div class="flex items-center mb-2">
                <img src="<?php echo htmlspecialchars($c['profile_picture'] ?? '/creavote/assets/avatar-placeholder.png'); ?>" class="w-8 h-8 rounded-full mr-2" alt="avatar">
                <span class="font-semibold"><?php echo htmlspecialchars($c['username']); ?></span>
            </div>
            <div class="text-gray-700 mb-2"><?php echo nl2br(htmlspecialchars($c['comment'])); ?></div>
            <div class="flex items-center gap-4 text-gray-500 text-sm">
                <span><?php echo date('M d, Y H:i', strtotime($c['created_at'])); ?></span>
                <!-- Add like/reply/etc. here if needed -->
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (!$comments): ?>
        <div class="text-gray-400 text-center py-8">No comments yet.</div>
    <?php endif; ?>
</div>
