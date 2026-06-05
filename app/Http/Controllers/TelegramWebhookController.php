<?php

namespace App\Http\Controllers;

use App\Services\Telegram\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function __construct(
        private readonly TelegramBotService $botService,
    ) {}

    public function handle(Request $request): Response
    {
        $secret = config('telegram.webhook_secret');

        if ($secret && $request->header('X-Telegram-Bot-Api-Secret-Token') !== $secret) {
            Log::warning('telegram.webhook_unauthorized', [
                'ip' => $request->ip(),
            ]);

            return response('Unauthorized', 401);
        }

        $update = $request->all();

        if (! is_array($update) || $update === []) {
            return response('OK', 200);
        }

        try {
            $this->botService->handleUpdate($update);
        } catch (\Throwable $exception) {
            Log::error('telegram.webhook_handler_failed', [
                'message' => $exception->getMessage(),
            ]);
        }

        return response('OK', 200);
    }
}
