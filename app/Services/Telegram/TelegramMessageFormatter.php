<?php

namespace App\Services\Telegram;

use App\Models\RsvpResponse;

class TelegramMessageFormatter
{
    public function rsvpAlert(RsvpResponse $response): string
    {
        $invitation = $response->invitation;
        $eventTitle = $invitation
            ? $this->escape($invitation->displayTitle().' — '.$invitation->event_type)
            : 'Tadbir';

        $guestName = $this->escape($response->guest_name);
        $statusLine = $this->escape($response->guestSummary());
        $timestamp = $this->escape($response->formattedTimestamp());

        $statusEmoji = $response->is_attending ? '✅' : '❌';

        return implode("\n", [
            '🔔 <b>Yangi RSVP javob keldi!</b>',
            '',
            '📌 <b>Tadbir:</b> '.$eventTitle,
            '👤 <b>Mehmon:</b> '.$guestName,
            $statusEmoji.' <b>Holati:</b> '.$statusLine,
            '🕒 <b>Vaqti:</b> '.$timestamp,
        ]);
    }

    public function accountLinked(string $userName): string
    {
        return implode("\n", [
            '✅ <b>Taklifnoma hisobingiz ulandi!</b>',
            '',
            'Salom, '.$this->escape($userName).'!',
            'Endi mehmonlar RSVP yuborganida darhol xabar olasiz.',
            '',
            'Buyruqlar:',
            '/status — ulanish holati',
            '/stop — xabarnomalarni o\'chirish',
        ]);
    }

    public function accountUnlinked(): string
    {
        return "🔕 <b>Xabarnomalar o'chirildi.</b>\n\nQayta ulash uchun kabinetdagi profil sahifasidan Telegram tugmasini bosing.";
    }

    public function statusMessage(bool $enabled, ?string $linkedAt): string
    {
        if (! $enabled) {
            return "🔕 <b>Xabarnomalar hozir o'chirilgan.</b>\n\nYoqish uchun profil sahifasiga o'ting.";
        }

        $linked = $linkedAt
            ? $this->escape($linkedAt)
            : '—';

        return implode("\n", [
            '📡 <b>Telegram ulanishi faol</b>',
            '',
            '🕒 <b>Ulangan vaqt:</b> '.$linked,
            '🔔 RSVP javoblari darhol yuboriladi.',
        ]);
    }

    public function welcomeHelp(): string
    {
        return implode("\n", [
            '👋 <b>Taklifnoma Telegram boti</b>',
            '',
            'RSVP xabarnomalarini olish uchun kabinetdagi profil sahifasidan <b>Telegram ulash</b> tugmasini bosing.',
        ]);
    }

    public function escape(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
