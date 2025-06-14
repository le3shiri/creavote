<?php
// Creavote Signup Controller
session_start();
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['first_name'] ?? '');
    $lastname = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstname.$lastname));
    $role = 'designer'; // Default role, can be changed by UI later
    $country = 'Morocco'; // Default, can be changed by UI later
    $errors = [];

    if (!$firstname || !$lastname || !$email || !$phone || !$password || !$confirm_password) {
        $errors[] = 'All fields are required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    // Check duplicate email or username
    $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = ? OR username = ?');
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        $errors[] = 'Email or username already registered.';
    }

    if ($errors) {
        $_SESSION['signup_errors'] = $errors;
        header('Location: ../../views/signup.php');
        exit;
    }

    // Generate user_id (short unique string)
    $user_id = uniqid('usr');
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (user_id, firstname, lastname, email, country, phone, username, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$user_id, $firstname, $lastname, $email, $country, $phone, $username, $password_hash, $role]);

    // Login user
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    $_SESSION['email'] = $email;
    header('Location: ../../views/home.php');
    exit;
} else {
    header('Location: ../../views/signup.php');
    exit;
}
