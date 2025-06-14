<?php
require_once __DIR__ . '/../config/db.php';
session_start();
header('Content-Type: application/json');
if (empty($_SESSION['user_id'])) {
    echo json_encode(['labels'=>[], 'data'=>[]]);
    exit;
}
$user_id = $_SESSION['user_id'];
// Get earnings by month (last 12 months)
$stmt = $pdo->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(amount) as total FROM prizes WHERE user_id = ? GROUP BY ym ORDER BY ym ASC LIMIT 12");
$stmt->execute([$user_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$labels = [];
$data = [];
foreach ($rows as $row) {
    $labels[] = $row['ym'];
    $data[] = (float)$row['total'];
}
echo json_encode(['labels'=>$labels, 'data'=>$data]);
