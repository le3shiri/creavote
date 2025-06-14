<?php
// Handle AJAX save/unsave offer
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');
if (empty($_SESSION['user_id']) || empty($_POST['offer_id'])) {
    echo json_encode(['success'=>false, 'message'=>'Unauthorized or missing offer ID.']);
    exit;
}
$user_id = $_SESSION['user_id'];
$offer_id = $_POST['offer_id'];
$action = $_POST['action'] ?? 'save';
if ($action === 'save') {
    // Save (if not already saved)
    $stmt = $pdo->prepare('SELECT 1 FROM saves WHERE user_id = ? AND design_id = ?');
    $stmt->execute([$user_id, $offer_id]);
    if (!$stmt->fetchColumn()) {
        $stmt = $pdo->prepare('INSERT INTO saves (user_id, design_id) VALUES (?, ?)');
        $stmt->execute([$user_id, $offer_id]);
    }
    echo json_encode(['success'=>true, 'saved'=>true]);
} else {
    // Unsave
    $stmt = $pdo->prepare('DELETE FROM saves WHERE user_id = ? AND design_id = ?');
    $stmt->execute([$user_id, $offer_id]);
    echo json_encode(['success'=>true, 'saved'=>false]);
}
