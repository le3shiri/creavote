<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false, 'message'=>'Unauthorized']);
    exit;
}
$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';
if ($action === 'mark_read' && isset($_GET['id'])) {
    $notif_id = (int)$_GET['id'];
    $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?');
    $stmt->execute([$notif_id, $user_id]);
    echo json_encode(['success'=>true]);
    exit;
}
if ($action === 'count_unread') {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
    $stmt->execute([$user_id]);
    $count = $stmt->fetchColumn();
    echo json_encode(['success'=>true, 'count'=>(int)$count]);
    exit;
}
// Mark all notifications as read when notifications page is loaded
$pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0')->execute([$user_id]);
// Default: fetch all notifications
$stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['success'=>true, 'notifications'=>$notifications]);
