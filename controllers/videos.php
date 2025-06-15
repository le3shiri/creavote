<?php
require_once __DIR__ . '/../config/db.php';
session_start();
header('Content-Type: application/json');
if (empty($_SESSION['user_id'])) {
    echo json_encode(['success'=>false, 'videos'=>[]]);
    exit;
}
// Fetch videos (designs with file_url ending in .mp4, .webm, .mov, etc)
$stmt = $pdo->prepare("SELECT d.*, u.firstname, u.lastname, u.profile_picture FROM designs d JOIN users u ON d.designer_id = u.user_id WHERE d.file_url LIKE '%.mp4' OR d.file_url LIKE '%.webm' OR d.file_url LIKE '%.mov' ORDER BY d.submitted_at DESC");
$stmt->execute();
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's saved design ids
$user_id = $_SESSION['user_id'];
$stmt2 = $pdo->prepare('SELECT design_id FROM saves WHERE user_id = ?');
$stmt2->execute([$user_id]);
$saved_ids = array_column($stmt2->fetchAll(PDO::FETCH_ASSOC), 'design_id');

// Add is_saved flag to each video
foreach ($videos as &$video) {
    $video['is_saved'] = in_array($video['design_id'], $saved_ids) ? 1 : 0;
}
unset($video);

echo json_encode(['success'=>true, 'videos'=>$videos]);
