<?php
require_once 'config.php';

header('Content-Type: application/json');

$telegram_id = $_POST['telegram_id'] ?? '';
$referral_code = $_POST['referral_code'] ?? '';

if (empty($telegram_id) || empty($referral_code)) {
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

// Check if user already has a referrer
if (!empty($user[0]['referred_by'])) {
    echo json_encode(['success' => false, 'error' => 'User already has a referrer']);
    exit;
}

// Get referrer
$referrer = $supabase->select('users', 'referral_code=eq.' . $referral_code);

if (empty($referrer)) {
    echo json_encode(['success' => false, 'error' => 'Invalid referral code']);
    exit;
}

$referrer_id = $referrer[0]['id'];

// Update user with referrer
$supabase->update('users', ['referred_by' => $referrer_id], 'id=eq.' . $user_id);

// Award bonus points to referrer
$referral_bonus = 100;
$new_referrer_points = $referrer[0]['points'] + $referral_bonus;
$supabase->update('users', ['points' => $new_referrer_points], 'id=eq.' . $referrer_id);

echo json_encode(['success' => true, 'message' => 'Referral code applied successfully']);
?>
