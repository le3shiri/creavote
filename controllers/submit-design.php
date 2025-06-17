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
$file_url = '/Creavote/uploads/' . $filename; // Always web-accessible path

// Validate file type (image or video)
$allowed_image_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$allowed_video_types = ['mp4', 'webm', 'mov'];
if (!in_array($ext, $allowed_image_types) && !in_array($ext, $allowed_video_types)) {
    die('Invalid file type. Only JPG, JPEG, PNG, GIF, WEBP images and MP4, WEBM, MOV videos are allowed.');
}

// Get description if provided
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

// Generate unique design_id
$design_id = uniqid('dsn');
// Insert into designs table with design_id
$stmt = $pdo->prepare('INSERT INTO designs (design_id, offer_id, designer_id, file_url, description) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$design_id, $offer_id, $user_id, $file_url, $description]);
// Insert into applications table if not already present
$stmt = $pdo->prepare('SELECT 1 FROM applications WHERE user_id = ? AND offer_id = ?');
$stmt->execute([$user_id, $offer_id]);
if (!$stmt->fetchColumn()) {
    $stmt = $pdo->prepare('INSERT INTO applications (user_id, offer_id) VALUES (?, ?)');
    $stmt->execute([$user_id, $offer_id]);
}
// Redirect based on file type
if (in_array($ext, $allowed_video_types)) {
    header('Location: ../views/videos.php');
    exit;
} else {
    header('Location: ../views/home.php');
    exit;
}
