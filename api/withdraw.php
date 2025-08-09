<?php
require_once 'config.php';

header('Content-Type: application/json');

$telegram_id = $_POST['telegram_id'] ?? '';
$method = $_POST['method'] ?? '';
$address = $_POST['address'] ?? '';
$points = $_POST['points'] ?? '';

if (empty($telegram_id) || empty($method) || empty($address) || empty($points)) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Get user
$user = $supabase->select('users', 'telegram_id=eq.' . $telegram_id);

if (empty($user)) {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit;
}

$user_id = $user[0]['id'];
$current_points = $user[0]['points'];

// Check if user has enough points
$min_withdraw_points = getSetting('min_withdraw_points');
if ($points < $min_withdraw_points) {
    echo json_encode(['success' => false, 'error' => 'Minimum withdrawal is ' . $min_withdraw_points . ' points']);
    exit;
}

if ($points > $current_points) {
    echo json_encode(['success' => false, 'error' => 'Insufficient points']);
    exit;
}

// Calculate USD amount
$usd_per_point = getSetting('usd_per_point');
$usd_amount = $points * $usd_per_point;

// Deduct points
$new_points = $current_points - $points;
$supabase->update('users', ['points' => $new_points], 'id=eq.' . $user_id);

// Record withdrawal
$withdrawal_record = [
    'user_id' => $user_id,
    'amount' => $usd_amount,
    'points_used' => $points,
    'method' => $method,
    'address' => $address,
    'status' => 'pending'
];
$supabase->insert('withdrawals', $withdrawal_record);

echo json_encode(['success' => true, 'usd_amount' => number_format($usd_amount, 2)]);
?>
