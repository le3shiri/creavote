<?php
// Creavote Login Controller
session_start();
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_username = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $errors = [];

    if (!$email_or_username || !$password) {
        $errors[] = 'Please fill in all fields.';
    }

    // Try to find user by email or username
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1');
    $stmt->execute([$email_or_username, $email_or_username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        $errors[] = 'Invalid email/username or password.';
    }

    if ($errors) {
        $_SESSION['login_errors'] = $errors;
        header('Location: ../../views/login.php');
        exit;
    }

    // Success: set session
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['email'] = $user['email'];
    // Optionally add more user info to session

    // Redirect to home or dashboard
    header('Location: ../../views/home.php');
    exit;
} else {
    header('Location: ../../views/login.php');
    exit;
}
