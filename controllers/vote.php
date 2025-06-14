<?php
// Handle voting on designs by voters
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../config/db.php';
if (empty($_SESSION['user_id'])) {
    die('Unauthorized.');
}
if (empty($_POST['design_id']) || empty($_POST['rating'])) {
    die('Missing data.');
}
$design_id = $_POST['design_id'];
$rating = (int)$_POST['rating'];
$voter_id = $_SESSION['user_id'];
if ($rating < 1 || $rating > 10) {
    die('Invalid rating.');
}
// Check if already voted
$stmt = $pdo->prepare('SELECT vote_id FROM votes WHERE voter_id = ? AND design_id = ?');
$stmt->execute([$voter_id, $design_id]);
if ($stmt->fetchColumn()) {
    die('You have already voted for this design.');
}
// Insert vote
$stmt = $pdo->prepare('INSERT INTO votes (voter_id, design_id, rating) VALUES (?, ?, ?)');
$stmt->execute([$voter_id, $design_id, $rating]);
// Update design's average rating and votes_count
$stmt = $pdo->prepare('SELECT AVG(rating) as avg_rating, COUNT(*) as total_votes FROM votes WHERE design_id = ?');
$stmt->execute([$design_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare('UPDATE designs SET rating = ?, votes_count = ? WHERE design_id = ?');
$stmt->execute([$row['avg_rating'], $row['total_votes'], $design_id]);
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
