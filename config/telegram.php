<?php

return [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'bot_username' => env('TELEGRAM_BOT_USERNAME', 'taklifnoma_bot'),
    'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
    'api_base_url' => 'https://api.telegram.org/bot',
    'link_token_ttl_minutes' => (int) env('TELEGRAM_LINK_TOKEN_TTL', 15),
    'enabled' => (bool) env('TELEGRAM_NOTIFICATIONS_ENABLED', true),
];
