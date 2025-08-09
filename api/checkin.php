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
$last_checkin = $user[0]['last_checkin'];
$checkin_streak = $user[0]['checkin_streak'];

$today = date('Y-m-d');

// Check if already checked in today
if ($last_checkin && date('Y-m-d', strtotime($last_checkin)) == $today) {
    echo json_encode(['success' => false, 'error' => 'Already checked in today']);
    exit;
}

// Calculate streak
$yesterday = date('Y-m-d', strtotime('-1 day'));
if ($last_checkin && date('Y-m-d', strtotime($last_checkin)) == $yesterday) {
    $checkin_streak++;
} else {
    $checkin_streak = 1;
}

// Determine points based on streak
$points = 0;
switch ($checkin_streak) {
    case 1:
        $points = 10;
        break;
    case 2:
        $points = 29;
        break;
    case 3:
        $points = 38;
        break;
    case 4:
        $points = 45;
        break;
    case 5:
        $points = 55;
        break;
    default:
        $points = 55;
        break;
}

// Update user points and streak
$new_points = $user[0]['points'] + $points;
$update_data = [
    'points' => $new_points,
    'last_checkin' => $today,
    'checkin_streak' => $checkin_streak
];
$supabase->update('users', $update_data, 'id=eq.' . $user_id);

// Record check-in
$checkin_record = [
    'user_id' => $user_id,
    'checkin_date' => $today,
    'points_earned' => $points
];
$supabase->insert('checkins', $checkin_record);

echo json_encode(['success' => true, 'points_earned' => $points, 'checkin_streak' => $checkin_streak]);
?>
