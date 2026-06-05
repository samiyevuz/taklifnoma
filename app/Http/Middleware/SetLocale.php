<?php

namespace App\Http\Middleware;

use App\Support\LocaleManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', config('locales.default', config('app.locale')));

        if (LocaleManager::isSupported($locale)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
