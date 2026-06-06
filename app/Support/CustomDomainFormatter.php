<?php

namespace App\Support;

use Illuminate\Support\Str;

class CustomDomainFormatter
{
    public static function suffix(): string
    {
        return (string) config('domains.custom_suffix', '.uz');
    }

    public static function prefix(string $templateSlug): string
    {
        return Str::slug($templateSlug, '-').'.';
    }

    public static function assemble(string $templateSlug, ?string $subdomain): ?string
    {
        $subdomain = Str::slug(trim((string) $subdomain), '-');

        if ($subdomain === '') {
            return null;
        }

        return strtolower(self::prefix($templateSlug).$subdomain.self::suffix());
    }

    public static function extractSubdomain(?string $fullDomain, string $templateSlug): string
    {
        if (blank($fullDomain)) {
            return '';
        }

        $full = strtolower(trim($fullDomain));
        $prefix = self::prefix($templateSlug);
        $suffix = self::suffix();

        if (str_starts_with($full, $prefix) && str_ends_with($full, $suffix)) {
            return substr($full, strlen($prefix), -strlen($suffix));
        }

        return $full;
    }

    public static function example(string $templateSlug, string $sample = 'farhod'): string
    {
        return self::assemble($templateSlug, $sample)
            ?? self::prefix($templateSlug).$sample.self::suffix();
    }
}
