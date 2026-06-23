<?php

namespace App\Support;

use Illuminate\Http\Request;

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

    public static function default(): string
    {
        return config('locales.default', 'uz');
    }

    public static function pattern(): string
    {
        return implode('|', self::codes());
    }

    public static function preferred(Request $request): string
    {
        $locale = $request->session()->get('locale')
            ?? $request->cookie('locale')
            ?? self::default();

        return self::isSupported($locale) ? $locale : self::default();
    }

    public static function home(?string $locale = null): string
    {
        $locale = $locale ?? self::current();

        return route('landing', ['locale' => $locale]);
    }

    public static function switchUrl(string $locale, ?Request $request = null): string
    {
        $request ??= request();

        if (! self::isSupported($locale)) {
            return self::home($locale);
        }

        $path = trim($request->path(), '/');
        $segments = $path === '' ? [] : explode('/', $path);

        if ($segments !== [] && self::isSupported($segments[0])) {
            $segments[0] = $locale;
        } else {
            array_unshift($segments, $locale);
        }

        $url = url(implode('/', $segments));

        if ($request->getQueryString()) {
            $url .= '?'.$request->getQueryString();
        }

        return $url;
    }
}
