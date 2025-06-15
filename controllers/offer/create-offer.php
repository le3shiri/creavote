<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ../../views/login.php');
    exit;
}
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['offer_title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $tags = trim($_POST['tags'] ?? ''); // comma-separated
    $offer_end = $_POST['offer_end'] ?? null;
    $budget = floatval($_POST['offer_budget'] ?? 0);
    $offer_start = date('Y-m-d H:i:s');

    if ($title && $category && $desc && $budget > 0) {
        $offer_id = uniqid('off');
        $stmt = $pdo->prepare('INSERT INTO offers (offer_id, user_id, offer_title, category, description, tags, offer_start, offer_end, offer_budget) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$offer_id, $user_id, $title, $category, $desc, $tags, $offer_start, $offer_end, $budget]);
        header('Location: ../../views/offer-details.php?id=' . $offer_id);
        exit;
    } else {
        header('Location: ../../views/create-offer.php?error=1');
        exit;
    }
}
header('Location: ../../views/create-offer.php');
exit;
