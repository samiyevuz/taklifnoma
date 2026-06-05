<?php

namespace App\Services\Telegram;

use App\Models\RsvpResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    public function __construct(
        private readonly TelegramMessageFormatter $formatter,
    ) {}

    public function isConfigured(): bool
    {
        return config('telegram.enabled')
            && filled(config('telegram.bot_token'));
    }

    public function sendRsvpAlert(RsvpResponse $response): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $response->loadMissing('invitation.user');
        $owner = $response->invitation?->user;

        if (! $owner?->telegram_chat_id || ! $owner->telegram_notifications_enabled) {
            return false;
        }

        return $this->sendHtmlMessage(
            $owner->telegram_chat_id,
            $this->formatter->rsvpAlert($response)
        );
    }

    public function sendHtmlMessage(string $chatId, string $html): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $token = config('telegram.bot_token');
        $url = config('telegram.api_base_url').$token.'/sendMessage';

        try {
            $result = Http::timeout(12)
                ->retry(2, 250)
                ->post($url, [
                    'chat_id' => $chatId,
                    'text' => $html,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true,
                ]);

            if (! $result->successful()) {
                Log::warning('telegram.send_message_failed', [
                    'chat_id' => $chatId,
                    'status' => $result->status(),
                    'body' => $result->json(),
                ]);

                return false;
            }

            return (bool) data_get($result->json(), 'ok', false);
        } catch (\Throwable $exception) {
            Log::error('telegram.send_message_exception', [
                'chat_id' => $chatId,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
