<?php
require_once 'config.php';

header('Content-Type: application/json');

$telegram_id = $_POST['telegram_id'] ?? '';

if (empty($telegram_id)) {
    echo json_encode(['success' => false, 'error' => 'Telegram ID is required']);
    exit;
}

// Get user
$user = $supabase->select('users', 'telegram_id=eq.' . $telegram_id);

if (empty($user)) {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit;
}

$user_id = $user[0]['id'];

// Check daily limit
$today = date('Y-m-d');
$ads_filter = "user_id=eq." . $user_id . "&watched_at=gte." . $today . "T00:00:00&watched_at=lte." . $today . "T23:59:59";
$ads_watched = $supabase->select('ads_watched', $ads_filter);
$ads_watched_today = count($ads_watched);
$daily_ad_limit = getSetting('daily_ad_limit');

if ($ads_watched_today >= $daily_ad_limit) {
    echo json_encode(['success' => false, 'error' => 'Daily ad limit reached']);
    exit;
}

// Award points
$points_per_ad = getSetting('points_per_ad');
$new_points = $user[0]['points'] + $points_per_ad;
$supabase->update('users', ['points' => $new_points], 'id=eq.' . $user_id);

// Record the ad watch
$ad_record = [
    'user_id' => $user_id,
    'watched_at' => date('c'),
    'points_earned' => $points_per_ad
];
$supabase->insert('ads_watched', $ad_record);

echo json_encode(['success' => true, 'points_earned' => $points_per_ad]);
?>
