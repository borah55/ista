<?php
require_once 'config.php';

header('Content-Type: application/json');

$telegram_id = $_GET['telegram_id'] ?? '';

if (empty($telegram_id)) {
    echo json_encode(['success' => false, 'error' => 'Telegram ID is required']);
    exit;
}

// Check if user exists
$user = $supabase->select('users', 'telegram_id=eq.' . $telegram_id);

if (empty($user)) {
    // Create new user
    $referral_code = generateReferralCode();
    $new_user = [
        'telegram_id' => $telegram_id,
        'referral_code' => $referral_code,
        'points' => 0,
        'checkin_streak' => 0
    ];
    
    $result = $supabase->insert('users', $new_user);
    $user_id = $result[0]['id'];
    $points = 0;
    $checkin_streak = 0;
    $last_checkin = null;
    $referrals_count = 0;
} else {
    $user_id = $user[0]['id'];
    $points = $user[0]['points'];
    $checkin_streak = $user[0]['checkin_streak'];
    $last_checkin = $user[0]['last_checkin'];
    
    // Get referrals count
    $referrals = $supabase->select('users', 'referred_by=eq.' . $user_id);
    $referrals_count = count($referrals);
}

// Check if user can check in today
$can_checkin = true;
if ($last_checkin) {
    $last_checkin_date = new DateTime($last_checkin);
    $today = new DateTime();
    if ($last_checkin_date->format('Y-m-d') == $today->format('Y-m-d')) {
        $can_checkin = false;
    }
}

// Get ads watched today
$today = date('Y-m-d');
$ads_filter = "user_id=eq." . $user_id . "&watched_at=gte." . $today . "T00:00:00&watched_at=lte." . $today . "T23:59:59";
$ads_watched = $supabase->select('ads_watched', $ads_filter);
$ads_watched_today = count($ads_watched);

$daily_ad_limit = getSetting('daily_ad_limit');

echo json_encode([
    'success' => true,
    'points' => $points,
    'checkin_streak' => $checkin_streak,
    'referrals_count' => $referrals_count,
    'ads_watched_today' => $ads_watched_today,
    'daily_ad_limit' => $daily_ad_limit,
    'can_checkin' => $can_checkin,
    'referral_code' => $user[0]['referral_code'] ?? $referral_code
]);

function generateReferralCode($length = 8) {
    return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', $length)), 0, $length);
}
?>
