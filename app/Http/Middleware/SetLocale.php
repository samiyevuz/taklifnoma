<?php

namespace App\Http\Middleware;

use App\Support\LocaleManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->route('locale') && LocaleManager::isSupported($request->route('locale'))) {
            return $next($request);
        }

        $locale = $request->session()->get('locale')
            ?? $request->cookie('locale')
            ?? config('locales.default', config('app.locale'));

        if (LocaleManager::isSupported($locale)) {
            app()->setLocale($locale);
            URL::defaults(['locale' => $locale]);
        }

        return $next($request);
    }
}
