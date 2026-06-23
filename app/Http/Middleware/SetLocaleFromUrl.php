<?php

namespace App\Http\Middleware;

use App\Support\LocaleManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (! is_string($locale) || ! LocaleManager::isSupported($locale)) {
            abort(404);
        }

        app()->setLocale($locale);
        $request->session()->put('locale', $locale);
        Cookie::queue('locale', $locale, 60 * 24 * 365);
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
