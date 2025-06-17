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

        // Handle offer image upload
        $offer_image = null;
        if (isset($_FILES['offer_image']) && $_FILES['offer_image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['offer_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('offer_', true) . '.' . $ext;
            $destination = __DIR__ . '/../../uploads/' . $filename;
            if (move_uploaded_file($_FILES['offer_image']['tmp_name'], $destination)) {
                $offer_image = '/Creavote/uploads/' . $filename;
            }
        }

        $stmt = $pdo->prepare('INSERT INTO offers (offer_id, user_id, offer_title, category, description, tags, offer_start, offer_end, offer_budget, offer_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$offer_id, $user_id, $title, $category, $desc, $tags, $offer_start, $offer_end, $budget, $offer_image]);
        // Notify all users except the creator
        $users_stmt = $pdo->query('SELECT user_id FROM users WHERE user_id != ' . $pdo->quote($user_id));
        $users = $users_stmt->fetchAll(PDO::FETCH_COLUMN);
        $msg = 'A new offer "' . htmlspecialchars($title, ENT_QUOTES) . '" has been posted!';
        foreach ($users as $notify_user_id) {
            $notif_stmt = $pdo->prepare('INSERT INTO notifications (user_id, type, design_id, message, is_read) VALUES (?, ?, ?, ?, 0)');
            $notif_stmt->execute([$notify_user_id, 'message', null, $msg]);
        }
        header('Location: ../../views/offer-details.php?id=' . $offer_id);
        exit;
    } else {
        header('Location: ../../views/create-offer.php?error=1');
        exit;
    }
}
header('Location: ../../views/create-offer.php');
exit;
