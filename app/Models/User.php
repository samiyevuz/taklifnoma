<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Support\ComplimentaryAccess;
use App\Models\PaymentInvoice;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'password', 'role', 'is_complimentary', 'telegram_chat_id', 'telegram_notifications_enabled', 'telegram_linked_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public const ROLE_USER = 'user';

    public const ROLE_ADMIN = 'admin';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'telegram_notifications_enabled' => 'boolean',
            'telegram_linked_at' => 'datetime',
            'is_complimentary' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function hasComplimentaryAccess(): bool
    {
        return ComplimentaryAccess::hasAccess($this);
    }

    public function paymentInvoices(): HasMany
    {
        return $this->hasMany(PaymentInvoice::class);
    }

    public function hasTelegramLinked(): bool
    {
        return filled($this->telegram_chat_id);
    }

    public function receivesTelegramNotifications(): bool
    {
        return $this->hasTelegramLinked() && $this->telegram_notifications_enabled;
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteSlugs(): array
    {
        return $this->favorites()->pluck('template_slug')->all();
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];
        $initials = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $initials ?: mb_strtoupper(mb_substr($this->name, 0, 1));
    }
}
