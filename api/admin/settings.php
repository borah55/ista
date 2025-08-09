<?php
require_once '../config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: /api/admin/auth.php');
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'points_per_ad' => $_POST['points-per-ad'] ?? '',
        'daily_ad_limit' => $_POST['daily-ad-limit'] ?? '',
        'min_withdraw_points' => $_POST['min-withdraw-points'] ?? '',
        'usd_per_point' => $_POST['usd-per-point'] ?? ''
    ];
    
    foreach ($settings as $key => $value) {
        $result = $supabase->update('admin_settings', ['setting_value' => $value], 'setting_key=eq.' . $key);
    }
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
