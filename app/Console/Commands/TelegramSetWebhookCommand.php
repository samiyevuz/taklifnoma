<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramSetWebhookCommand extends Command
{
    protected $signature = 'telegram:set-webhook {--remove : Remove the current webhook}';

    protected $description = 'Register or remove the Telegram bot webhook endpoint';

    public function handle(): int
    {
        $token = config('telegram.bot_token');

        if (! filled($token)) {
            $this->error('TELEGRAM_BOT_TOKEN is not configured.');

            return self::FAILURE;
        }

        $apiUrl = config('telegram.api_base_url').$token;

        if ($this->option('remove')) {
            $response = Http::post($apiUrl.'/deleteWebhook');
            $this->line(json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return $response->successful() ? self::SUCCESS : self::FAILURE;
        }

        $payload = [
            'url' => route('telegram.webhook'),
            'allowed_updates' => ['message'],
        ];

        if ($secret = config('telegram.webhook_secret')) {
            $payload['secret_token'] = $secret;
        }

        $response = Http::post($apiUrl.'/setWebhook', $payload);
        $this->line(json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $response->successful() ? self::SUCCESS : self::FAILURE;
    }
}
