<?php
session_start();
require_once '../config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $description = trim($_POST['description'] ?? '');
    $offer_id = isset($_POST['offer_id']) ? intval($_POST['offer_id']) : 0;
    if (!$offer_id) {
        $_SESSION['upload_error'] = 'Please select an offer to apply your video.';
        header('Location: ../views/add_video.php');
        exit;
    }
    $upload_dir = __DIR__ . '/../uploads/videos/';
    $allowed_types = ['video/mp4', 'video/webm', 'video/quicktime'];

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!isset($_FILES['video_file']) || $_FILES['video_file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['upload_error'] = 'Please select a valid video file to upload.';
        header('Location: ../views/add_video.php');
        exit;
    }

    $file = $_FILES['video_file'];
    $file_type = mime_content_type($file['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        $_SESSION['upload_error'] = 'Invalid file type. Only MP4, WEBM, and MOV videos are allowed.';
        header('Location: ../views/add_video.php');
        exit;
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_name = uniqid('video_', true) . '.' . $ext;
    $target_path = $upload_dir . $new_name;
    $db_path = '/uploads/videos/' . $new_name;

    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        $_SESSION['upload_error'] = 'Failed to save the uploaded file.';
        header('Location: ../views/add_video.php');
        exit;
    }

    // Insert into designs table
    $stmt = $pdo->prepare('INSERT INTO designs (designer_id, offer_id, file_url, description, created_at) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([$user_id, $offer_id, $db_path, $description]);
    $video_id = $pdo->lastInsertId();
    $_SESSION['last_uploaded_video_id'] = $video_id;

    header('Location: ../views/videos.php');
    exit;
} else {
    header('Location: ../views/add_video.php');
    exit;
}
