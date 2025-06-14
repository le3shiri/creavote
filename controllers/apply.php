<?php
// Handle AJAX apply to offer
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
// Check if already applied
$stmt = $pdo->prepare('SELECT 1 FROM applications WHERE user_id = ? AND offer_id = ?');
$stmt->execute([$user_id, $offer_id]);
if ($stmt->fetchColumn()) {
    echo json_encode(['success'=>false, 'message'=>'Already applied.']);
    exit;
}
// Insert application
$stmt = $pdo->prepare('INSERT INTO applications (user_id, offer_id) VALUES (?, ?)');
$stmt->execute([$user_id, $offer_id]);
echo json_encode(['success'=>true]);
