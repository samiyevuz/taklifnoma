<?php

namespace App\Listeners;

use App\Events\RsvpResponseSubmitted;
use App\Services\Telegram\TelegramNotificationService;

class SendRsvpTelegramNotification
{
    public function __construct(
        private readonly TelegramNotificationService $telegram,
    ) {}

    public function handle(RsvpResponseSubmitted $event): void
    {
        $this->telegram->sendRsvpAlert($event->response);
    }
}
