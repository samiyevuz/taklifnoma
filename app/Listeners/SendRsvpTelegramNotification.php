<?php

namespace App\Listeners;

use App\Events\RsvpResponseSubmitted;
use App\Services\Telegram\TelegramNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRsvpTelegramNotification implements ShouldQueue
{
    public bool $afterCommit = true;

    public string $queue = 'notifications';

    public function __construct(
        private readonly TelegramNotificationService $telegram,
    ) {}

    public function handle(RsvpResponseSubmitted $event): void
    {
        $this->telegram->sendRsvpAlert($event->response);
    }
}
