<?php
require_once 'config.php';

// Set webhook
$url = "https://api.telegram.org/bot" . BOT_TOKEN . "/setWebhook";

$data = [
    'url' => WEBHOOK_URL . '/api/bot.php'
];

$options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

// Return response
header('Content-Type: application/json');
echo $result;
?>
