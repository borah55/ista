<?php
require_once 'config.php';

// Get webhook data
$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['message'])) {
    $message = $update['message'];
    $chat_id = $message['chat']['id'];
    $text = $message['text'];
    
    // Handle /start command with referral
    if (strpos($text, '/start') === 0) {
        $params = explode(' ', $text);
        $referral_code = isset($params[1]) ? $params[1] : '';
        
        // Get user info from Telegram
        $telegram_id = $message['from']['id'];
        $username = $message['from']['username'] ?? '';
        
        // Check if user exists
        $user = $supabase->select('users', 'telegram_id=eq.' . $telegram_id);
        
        if (empty($user)) {
            // Generate referral code
            $referral_code_new = generateReferralCode();
            
            // Create new user
            $new_user = [
                'telegram_id' => $telegram_id,
                'username' => $username,
                'referral_code' => $referral_code_new,
                'points' => 0,
                'checkin_streak' => 0
            ];
            
            $result = $supabase->insert('users', $new_user);
            
            // Apply referral if code provided
            if (!empty($referral_code)) {
                // Get referrer
                $referrer = $supabase->select('users', 'referral_code=eq.' . $referral_code);
                
                if (!empty($referrer)) {
                    $referrer_id = $referrer[0]['id'];
                    
                    // Update user with referrer
                    $supabase->update('users', ['referred_by' => $referrer_id], 'id=eq.' . $result[0]['id']);
                    
                    // Award bonus points to referrer
                    $referrer_points = $referrer[0]['points'] + 100;
                    $supabase->update('users', ['points' => $referrer_points], 'id=eq.' . $referrer_id);
                }
            }
        }
        
        // Send welcome message with mini app link
        $app_url = "https://your-vercel-app.vercel.app/";
        $reply = "Welcome to Earn Points Mini App!\n\nClick the button below to start earning points:";
        
        $keyboard = [
            ['text' => 'Open Earn Points App', 'web_app' => ['url' => $app_url]]
        ];
        
        $reply_markup = [
            'keyboard' => [$keyboard],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
        
        sendTelegramMessage($chat_id, $reply, $reply_markup);
    }
}

function sendTelegramMessage($chat_id, $text, $reply_markup = []) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage";
    
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    
    if (!empty($reply_markup)) {
        $data['reply_markup'] = json_encode($reply_markup);
    }
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    return $result;
}

function generateReferralCode($length = 8) {
    return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', $length)), 0, $length);
}
?>
