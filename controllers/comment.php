<?php
// Handle comment submission via AJAX
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../config/db.php';
// Debug log
file_put_contents(__DIR__.'/comment_debug.log', json_encode([
    'user_id' => $_SESSION['user_id'] ?? null,
    'design_id' => $_POST['design_id'] ?? null,
    'comment' => $_POST['comment'] ?? null,
    'designs_in_db' => $pdo->query('SELECT design_id FROM designs')->fetchAll(PDO::FETCH_COLUMN),
    'users_in_db' => $pdo->query('SELECT user_id FROM users')->fetchAll(PDO::FETCH_COLUMN),
], JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

header('Content-Type: application/json');
if (empty($_SESSION['user_id']) || empty($_POST['design_id']) || empty($_POST['comment'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized or missing data.']);
    exit;
}
$user_id = $_SESSION['user_id'];
$design_id = $_POST['design_id'];
$comment = trim($_POST['comment']);
if ($comment === '') {
    echo json_encode(['success' => false, 'message' => 'Comment cannot be empty.']);
    exit;
}
// Insert comment
$stmt = $pdo->prepare('INSERT INTO comments (design_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())');
$stmt->execute([$design_id, $user_id, $comment]);
$comment_id = $pdo->lastInsertId();
// Fetch user info for display
$stmt = $pdo->prepare('SELECT username, profile_picture FROM users WHERE user_id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode([
    'success' => true,
    'comment' => [
        'comment_id' => $comment_id,
        'username' => $user['username'],
        'profile_picture' => $user['profile_picture'],
        'comment' => htmlspecialchars($comment),
        'created_at' => date('Y-m-d H:i'),
    ]
]);
