<?php
session_start();
require_once '../../config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ../../views/login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $errors = [];

    // Fetch user hash
    $stmt = $pdo->prepare('SELECT password FROM users WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $old_hash = $row ? $row['password'] : null;
    if (!$row || !password_verify($current_password, $row['password'])) {
        $errors[] = 'Current password is incorrect.';
    }

    // Validate new password
    if (strlen($new_password) < 8) {
        $errors[] = 'New password must be at least 8 characters.';
    }
    if (!preg_match('/[A-Z]/', $new_password)) {
        $errors[] = 'New password must contain at least one uppercase letter.';
    }
    if (!preg_match('/[a-z]/', $new_password)) {
        $errors[] = 'New password must contain at least one lowercase letter.';
    }
    if (!preg_match('/[0-9]/', $new_password)) {
        $errors[] = 'New password must contain at least one number.';
    }
    if (!preg_match('/[^A-Za-z0-9]/', $new_password)) {
        $errors[] = 'New password must contain at least one special character.';
    }
    if ($current_password === $new_password) {
        $errors[] = 'New password must be different from the current password.';
    }

    if ($errors) {
        $_SESSION['password_change_errors'] = $errors;
        header('Location: ../../views/profile-settings.php');
        exit;
    }

    // Update password
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE user_id = ?');
    $stmt->execute([$new_hash, $user_id]);

    // Fetch new hash for debug
    $stmt = $pdo->prepare('SELECT password FROM users WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $updated_row = $stmt->fetch(PDO::FETCH_ASSOC);
    $updated_hash = $updated_row ? $updated_row['password'] : null;

    $_SESSION['password_change_success'] = 'Password updated successfully!';
    $_SESSION['password_debug'] = [
        'old_hash' => $old_hash,
        'new_hash' => $new_hash,
        'updated_hash' => $updated_hash
    ];
    header('Location: ../../views/profile-settings.php');
    exit;
} else {
    header('Location: ../../views/profile-settings.php');
    exit;
}
