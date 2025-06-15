<?php
// Handle AJAX apply to offer
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');
// Debug logging
$log_path = __DIR__.'/apply_debug.log';
$log_ok = true;
$log_msg = '';
try {
    if (!file_exists($log_path)) {
        if (!touch($log_path)) {
            $log_ok = false;
            $log_msg = 'Log file could not be created.';
        }
    }
    if (!is_writable($log_path)) {
        $log_ok = false;
        $log_msg = 'Log file is not writable.';
    }
    if ($log_ok) {
        file_put_contents($log_path, date('c') . "\nPOST: " . json_encode($_POST) . "\nSESSION: " . json_encode($_SESSION) . "\n", FILE_APPEND);
    }
} catch (Exception $e) {
    $log_ok = false;
    $log_msg = 'Log error: ' . $e->getMessage();
}
if (empty($_SESSION['user_id']) || empty($_POST['offer_id'])) {
    if ($log_ok) file_put_contents($log_path, date('c') . "\nERROR: Unauthorized or missing offer ID\n", FILE_APPEND);
    echo json_encode(['success'=>false, 'message'=>'Unauthorized or missing offer ID.', 'log'=>$log_ok ? 'ok' : $log_msg]);
    exit;
}
$user_id = $_SESSION['user_id'];
$offer_id = $_POST['offer_id'];
echo json_encode(['success'=>true, 'log'=>$log_ok ? 'ok' : $log_msg]);
