<?php
// Handle AJAX save/unsave offer
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');
// Debug logging
file_put_contents(__DIR__.'/save_debug.log', date('c') . "\nPOST: " . json_encode($_POST) . "\nSESSION: " . json_encode($_SESSION) . "\n", FILE_APPEND);
if (empty($_SESSION['user_id']) || empty($_POST['design_id'])) {
    echo json_encode(['success'=>false, 'message'=>'Unauthorized or missing design ID.']);
    exit;
}
$user_id = $_SESSION['user_id'];
$design_id = $_POST['design_id'];
$action = $_POST['action'] ?? 'save';
if ($action === 'save') {
    // Save (if not already saved)
    $stmt = $pdo->prepare('SELECT 1 FROM saves WHERE user_id = ? AND design_id = ?');
    $stmt->execute([$user_id, $design_id]);
    if (!$stmt->fetchColumn()) {
        $stmt = $pdo->prepare('INSERT INTO saves (user_id, design_id) VALUES (?, ?)');
        $stmt->execute([$user_id, $design_id]);
    }
    echo json_encode(['success'=>true, 'saved'=>true]);
} else {
    // Unsave
    $stmt = $pdo->prepare('DELETE FROM saves WHERE user_id = ? AND design_id = ?');
    $stmt->execute([$user_id, $design_id]);
    echo json_encode(['success'=>true, 'saved'=>false]);
}
