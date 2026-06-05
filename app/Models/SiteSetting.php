<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $settings = self::cached();

        if (array_key_exists($key, $settings)) {
            return $settings[$key];
        }

        return $default;
    }

    public static function setValue(string $key, ?string $value): void
    {
        self::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        self::clearCache();
    }

    public static function setMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            self::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        self::clearCache();
    }

    public static function cached(): array
    {
        return Cache::rememberForever('site_settings', function () {
            return self::query()->pluck('value', 'key')->all();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('site_settings');
        Cache::forget('landing.templates');
        Cache::forget('landing.faqs');
    }
}
