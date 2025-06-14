<?php
// Handle design submission for offer application
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../config/db.php';
if (empty($_SESSION['user_id']) || empty($_POST['offer_id'])) {
    die('Unauthorized.');
}
$user_id = $_SESSION['user_id'];
$offer_id = $_POST['offer_id'];
// File upload handling
if (!isset($_FILES['design_file']) || $_FILES['design_file']['error'] !== UPLOAD_ERR_OK) {
    die('Please upload a file.');
}
$upload_dir = __DIR__ . '/../uploads/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
// Sanitize filename: remove spaces/special chars, force lowercase extension
$original_name = basename($_FILES['design_file']['name']);
$ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
$name = preg_replace('/[^a-zA-Z0-9-_]/', '_', pathinfo($original_name, PATHINFO_FILENAME));
$filename = uniqid('design_', true) . '_' . $name . '.' . $ext;
$target = $upload_dir . $filename;
// Try to move file and log errors if any
if (!move_uploaded_file($_FILES['design_file']['tmp_name'], $target)) {
    error_log('Failed to upload file: ' . print_r($_FILES['design_file'], true));
    die('Failed to upload file.');
}
$file_url = '/uploads/' . $filename; // Always web-accessible path
// Insert into designs table
$stmt = $pdo->prepare('INSERT INTO designs (offer_id, designer_id, file_url, description) VALUES (?, ?, ?, ?)');
$stmt->execute([$offer_id, $user_id, $file_url, '']);
header('Location: ../views/apply-offer.php?offer_id=' . urlencode($offer_id) . '&success=1');
exit;
