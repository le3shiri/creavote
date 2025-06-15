<?php
// Handle AJAX voting on designs by voters
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';
if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
    exit;
}
if (empty($_POST['design_id']) || empty($_POST['rating'])) {
    echo json_encode(['success' => false, 'error' => 'Missing data.']);
    exit;
}
$design_id = $_POST['design_id'];
$rating = (int)$_POST['rating'];
$voter_id = $_SESSION['user_id'];
if ($rating < 1 || $rating > 10) {
    echo json_encode(['success' => false, 'error' => 'Invalid rating.']);
    exit;
}
// Check if already voted
$stmt = $pdo->prepare('SELECT vote_id FROM votes WHERE voter_id = ? AND design_id = ?');
$stmt->execute([$voter_id, $design_id]);
if ($stmt->fetchColumn()) {
    echo json_encode(['success' => false, 'error' => 'You have already voted for this design.']);
    exit;
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
echo json_encode([
    'success' => true,
    'rating' => $rating,
    'avg_rating' => round($row['avg_rating'], 1),
    'votes_count' => (int)$row['total_votes'],
]);
