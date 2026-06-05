<?php

namespace App\Support;

class LocaleManager
{
    public static function codes(): array
    {
        return array_keys(config('locales.supported', []));
    }

    public static function isSupported(string $locale): bool
    {
        return in_array($locale, self::codes(), true);
    }

    public static function current(): string
    {
        $locale = app()->getLocale();

        return self::isSupported($locale) ? $locale : config('locales.default', 'uz');
    }

    public static function meta(string $locale): ?array
    {
        return config("locales.supported.{$locale}");
    }
}
