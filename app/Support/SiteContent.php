<?php

namespace App\Support;

use App\Models\LandingFaq;
use App\Models\SiteSetting;

class SiteContent
{
    public static function contact(string $key): ?string
    {
        $stored = SiteSetting::getValue("contact.{$key}");

        if ($stored !== null && $stored !== '') {
            return $stored;
        }

        return config("social.{$key}");
    }

    public static function contactFilled(string $key): bool
    {
        return filled(self::contact($key));
    }

    public static function faqMeta(): array
    {
        $locale = app()->getLocale();

        return [
            'label' => SiteSetting::getValue("faq.{$locale}.label") ?? __('landing.faq_label'),
            'title' => SiteSetting::getValue("faq.{$locale}.title") ?? __('landing.faq_title'),
            'desc' => SiteSetting::getValue("faq.{$locale}.desc") ?? __('landing.faq_desc'),
        ];
    }

    public static function faqs(): array
    {
        if (! LandingFaq::query()->active()->exists()) {
            $fallback = __('landing.faqs');

            return is_array($fallback) ? $fallback : [];
        }

        return LandingFaq::query()
            ->active()
            ->ordered()
            ->get()
            ->map(fn (LandingFaq $faq) => $faq->toFaqArray())
            ->filter(fn (array $item) => filled($item['q']))
            ->values()
            ->all();
    }
}
