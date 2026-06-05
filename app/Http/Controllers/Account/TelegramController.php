<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function __construct(
        private readonly TelegramBotService $botService,
    ) {}

    public function connect(): RedirectResponse
    {
        $url = $this->botService->createConnectUrl(auth()->user());

        return redirect()->away($url);
    }

    public function disconnect(): RedirectResponse
    {
        $this->botService->disconnect(auth()->user());

        return back()->with('success', __('account.telegram_disconnected'));
    }

    public function toggle(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (! $user->telegram_chat_id) {
            return response()->json([
                'success' => false,
                'message' => __('account.telegram_not_linked'),
            ], 422);
        }

        $enabled = $request->boolean('enabled', ! $user->telegram_notifications_enabled);

        $user->update([
            'telegram_notifications_enabled' => $enabled,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'enabled' => $enabled,
            ],
        ]);
    }
}
