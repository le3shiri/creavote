<?php
// Hide (soft-delete) a design before the offer deadline
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../../config/db.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || empty($_POST['design_id'])) {
    echo json_encode(['success'=>false, 'message'=>'Unauthorized or missing design ID.']);
    exit;
}
$user_id = $_SESSION['user_id'];
$design_id = $_POST['design_id'];

// Fetch design info and offer deadline
$stmt = $pdo->prepare('SELECT d.designer_id, d.offer_id, o.deadline FROM designs d JOIN offers o ON d.offer_id = o.offer_id WHERE d.design_id = ?');
$stmt->execute([$design_id]);
$design = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$design) {
    echo json_encode(['success'=>false, 'message'=>'Design not found.']);
    exit;
}
if ($design['designer_id'] !== $user_id) {
    echo json_encode(['success'=>false, 'message'=>'You are not the owner of this design.']);
    exit;
}
$current_time = date('Y-m-d H:i:s');
if ($current_time > $design['deadline']) {
    echo json_encode(['success'=>false, 'message'=>'You cannot delete/hide your design after the offer deadline.']);
    exit;
}
// Hide the design (set is_hidden=1)
$stmt = $pdo->prepare('UPDATE designs SET is_hidden=1 WHERE design_id = ?');
$stmt->execute([$design_id]);
echo json_encode(['success'=>true, 'message'=>'Design hidden successfully.']);
