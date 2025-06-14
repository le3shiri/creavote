<?php
// Profile Edit Controller
session_start();
require_once '../../config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ../../views/login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $display_name = trim($_POST['display_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $linkedin = trim($_POST['linkedin'] ?? '');
    $instagram = trim($_POST['instagram'] ?? '');
    $twitter = trim($_POST['twitter'] ?? '');
    $errors = [];

    // Basic validation
    if (!$firstname || !$lastname || !$email) {
        $errors[] = 'First name, last name, and email are required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }

    // Profile picture upload
    $profile_picture_url = null;
    // Only process upload if not removing picture
    if (isset($_POST['remove_picture']) && $_POST['remove_picture'] == '1') {
        $profile_picture_url = null;
        file_put_contents(__DIR__.'/edit_profile_debug.log', date('c') . " - Remove picture requested by user $user_id\n", FILE_APPEND);
    } elseif (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['profile_picture']['size'] <= 2*1024*1024) {
            $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
            $destination = '../../uploads/' . $filename;
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
                $profile_picture_url = '/uploads/' . $filename;
                file_put_contents(__DIR__.'/edit_profile_debug.log', date('c') . " - Uploaded new picture for user $user_id: $filename\n", FILE_APPEND);
            } else {
                $errors[] = 'Failed to move uploaded file.';
                file_put_contents(__DIR__.'/edit_profile_debug.log', date('c') . " - Failed to move uploaded file for user $user_id\n", FILE_APPEND);
            }
        } else {
            $errors[] = 'Invalid profile picture file.';
            file_put_contents(__DIR__.'/edit_profile_debug.log', date('c') . " - Invalid file type or size for user $user_id\n", FILE_APPEND);
        }
    }

    if ($errors) {
        $_SESSION['profile_edit_errors'] = $errors;
        header('Location: ../../views/profile-edit.php');
        exit;
    }

    // Update user in DB
    $sql = 'UPDATE users SET firstname=?, lastname=?, email=?, phone=?, country=?';
    $params = [$firstname, $lastname, $email, $phone, $country];
    if ($profile_picture_url !== null) {
        $sql .= ', profile_picture=?';
        $params[] = $profile_picture_url;
    }
    $sql .= ' WHERE user_id=?';
    $params[] = $user_id;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $_SESSION['profile_edit_success'] = 'Profile updated successfully!';
    header('Location: ../../views/profile-edit.php');
    exit;
} else {
    header('Location: ../../views/profile-edit.php');
    exit;
}
