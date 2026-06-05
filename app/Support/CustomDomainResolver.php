<?php

namespace App\Support;

use App\Models\Invitation;
use Illuminate\Http\Request;

class CustomDomainResolver
{
    public static function appHost(): ?string
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        return $host ? strtolower($host) : null;
    }

    public static function isCustomHost(string $host): bool
    {
        $host = strtolower($host);
        $appHost = self::appHost();

        if (! $appHost || $host === $appHost) {
            return false;
        }

        return ! in_array($host, ['www.'.$appHost, 'localhost', '127.0.0.1'], true);
    }

    public static function findByHost(string $host): ?Invitation
    {
        if (! self::isCustomHost($host)) {
            return null;
        }

        return Invitation::query()
            ->where('custom_domain', strtolower($host))
            ->where('status', Invitation::STATUS_ACTIVE)
            ->first();
    }

    public static function findForRequest(Request $request): ?Invitation
    {
        return self::findByHost($request->getHost());
    }
}
