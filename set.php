<?php
// Load composer
require __DIR__ . '/constant.php';
require __DIR__ . '/vendor/autoload.php';

$API_KEY = APIKEY;
$BOT_NAME = BOTNAME;
$hook_url = 'https://telegram.rohs.ch/hook.php';
echo "Setting syzinBot";
try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($API_KEY, $BOT_NAME);

    // Set webhook
    $result = $telegram->setWebhook($hook_url);
    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e;
}
