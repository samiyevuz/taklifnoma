<?php

namespace App\Services\Telegram;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TelegramBotService
{
    public function __construct(
        private readonly TelegramNotificationService $notifications,
        private readonly TelegramMessageFormatter $formatter,
    ) {}

    public function createConnectUrl(User $user): string
    {
        $token = Str::random(48);

        Cache::put(
            $this->linkCacheKey($token),
            $user->id,
            now()->addMinutes(config('telegram.link_token_ttl_minutes', 15))
        );

        $username = ltrim((string) config('telegram.bot_username'), '@');

        return 'https://t.me/'.$username.'?start='.$token;
    }

    public function handleUpdate(array $update): void
    {
        $message = $update['message'] ?? null;

        if (! is_array($message)) {
            return;
        }

        $chatId = (string) ($message['chat']['id'] ?? '');
        $text = trim((string) ($message['text'] ?? ''));

        if ($chatId === '' || $text === '') {
            return;
        }

        if (str_starts_with($text, '/start')) {
            $this->handleStartCommand($chatId, $text);

            return;
        }

        if ($text === '/stop') {
            $this->handleStopCommand($chatId);

            return;
        }

        if ($text === '/status') {
            $this->handleStatusCommand($chatId);

            return;
        }

        $this->notifications->sendHtmlMessage(
            $chatId,
            $this->formatter->welcomeHelp()
        );
    }

    private function handleStartCommand(string $chatId, string $text): void
    {
        $parts = preg_split('/\s+/', $text, 2);
        $token = $parts[1] ?? null;

        if (! $token) {
            $this->notifications->sendHtmlMessage(
                $chatId,
                $this->formatter->welcomeHelp()
            );

            return;
        }

        $userId = Cache::pull($this->linkCacheKey($token));

        if (! $userId) {
            $this->notifications->sendHtmlMessage(
                $chatId,
                "⚠️ <b>Ulanish havolasi eskirgan yoki noto'g'ri.</b>\n\nIltimos, kabinetdagi profil sahifasidan qayta urinib ko'ring."
            );

            return;
        }

        $user = User::query()->find($userId);

        if (! $user) {
            return;
        }

        $linkedAt = now()->timezone('Asia/Tashkent')->format('d.m.Y H:i');

        $user->forceFill([
            'telegram_chat_id' => $chatId,
            'telegram_notifications_enabled' => true,
            'telegram_linked_at' => now(),
        ])->save();

        $this->notifications->sendHtmlMessage(
            $chatId,
            $this->formatter->accountLinked($user->name)
        );
    }

    private function handleStopCommand(string $chatId): void
    {
        $user = User::query()->where('telegram_chat_id', $chatId)->first();

        if (! $user) {
            $this->notifications->sendHtmlMessage(
                $chatId,
                $this->formatter->welcomeHelp()
            );

            return;
        }

        $user->forceFill([
            'telegram_notifications_enabled' => false,
        ])->save();

        $this->notifications->sendHtmlMessage(
            $chatId,
            $this->formatter->accountUnlinked()
        );
    }

    private function handleStatusCommand(string $chatId): void
    {
        $user = User::query()->where('telegram_chat_id', $chatId)->first();

        if (! $user) {
            $this->notifications->sendHtmlMessage(
                $chatId,
                $this->formatter->welcomeHelp()
            );

            return;
        }

        $linkedAt = $user->telegram_linked_at
            ? $user->telegram_linked_at->timezone('Asia/Tashkent')->format('d.m.Y H:i')
            : null;

        $this->notifications->sendHtmlMessage(
            $chatId,
            $this->formatter->statusMessage(
                (bool) $user->telegram_notifications_enabled,
                $linkedAt
            )
        );
    }

    public function disconnect(User $user): void
    {
        if ($user->telegram_chat_id) {
            $this->notifications->sendHtmlMessage(
                $user->telegram_chat_id,
                $this->formatter->accountUnlinked()
            );
        }

        $user->forceFill([
            'telegram_chat_id' => null,
            'telegram_notifications_enabled' => false,
            'telegram_linked_at' => null,
        ])->save();
    }

    private function linkCacheKey(string $token): string
    {
        return 'telegram:link:'.$token;
    }
}
