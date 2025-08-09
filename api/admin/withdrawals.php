<?php
require_once '../config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: /api/admin/auth.php');
    exit;
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

if (empty($action) || empty($id)) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

if ($action === 'approve') {
    $status = 'completed';
} elseif ($action === 'reject') {
    $status = 'rejected';
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}

$result = $supabase->update('withdrawals', ['status' => $status], 'id=eq.' . $id);

if (isset($result[0]) && isset($result[0]['id'])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>
