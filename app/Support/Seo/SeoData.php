<?php

namespace App\Support\Seo;

use App\Models\Invitation;
use App\Support\LocaleManager;
use App\Support\TemplateCatalog;

final class SeoData
{
    /**
     * @param  array<string, string>  $hreflang
     * @param  list<array<string, mixed>>  $jsonLd
     */
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?string $canonical = null,
        public readonly bool $indexable = true,
        public readonly array $hreflang = [],
        public readonly array $jsonLd = [],
        public readonly string $ogType = 'website',
        public readonly ?string $ogImage = null,
    ) {}

    public static function forLanding(string $title, string $description): self
    {
        $locale = LocaleManager::current();

        return new self(
            title: $title,
            description: $description,
            canonical: self::localizedUrl($locale),
            indexable: true,
            hreflang: self::hreflangForPath(''),
            jsonLd: self::landingJsonLd($locale, $description),
        );
    }

    public static function forPreview(string $templateSlug, string $title, string $description): self
    {
        $locale = LocaleManager::current();
        $path = 'preview/'.$templateSlug;
        $template = TemplateCatalog::find($templateSlug);
        $ogImage = is_array($template) ? ($template['cover_url'] ?? null) : null;

        return new self(
            title: $title,
            description: $description,
            canonical: self::localizedUrl($locale, $path),
            indexable: true,
            hreflang: self::hreflangForPath($path),
            ogImage: $ogImage,
        );
    }

    public static function forInvitation(Invitation $invitation, string $title, string $description): self
    {
        $locale = LocaleManager::current();
        $slug = $invitation->custom_slug ?: $invitation->slug;

        return new self(
            title: $title,
            description: $description,
            canonical: self::localizedUrl($locale, 'l/'.$slug),
            indexable: false,
            ogImage: $invitation->resolvedCoverUrl(),
            ogType: 'website',
        );
    }

    public static function noindex(?string $title = null, ?string $description = null): self
    {
        return new self(
            title: $title,
            description: $description,
            indexable: false,
        );
    }

    public static function invitationDescription(Invitation $invitation): string
    {
        return __('invitation.meta_description_for', [
            'couple' => $invitation->coupleTitle(),
            'event' => $invitation->event_type,
        ]);
    }

    /**
     * @return array<string, string>
     */
    private static function hreflangForPath(string $pathAfterLocale): array
    {
        $pathAfterLocale = trim($pathAfterLocale, '/');
        $suffix = $pathAfterLocale === '' ? '/' : '/'.$pathAfterLocale;
        $alternates = [];

        foreach (LocaleManager::codes() as $locale) {
            $alternates[$locale] = self::siteBase().'/'.$locale.$suffix;
        }

        $default = LocaleManager::default();
        $alternates['x-default'] = $alternates[$default] ?? reset($alternates);

        return $alternates;
    }

    private static function localizedUrl(string $locale, string $pathAfterLocale = ''): string
    {
        $pathAfterLocale = trim($pathAfterLocale, '/');

        if ($pathAfterLocale === '') {
            return self::siteBase().'/'.$locale.'/';
        }

        return self::siteBase().'/'.$locale.'/'.$pathAfterLocale;
    }

    private static function siteBase(): string
    {
        return rtrim((string) config('seo.site_url'), '/');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function landingJsonLd(string $locale, string $description): array
    {
        $base = self::siteBase();
        $landingUrl = self::localizedUrl($locale);

        $languageMap = [
            'uz' => 'uz-UZ',
            'ru' => 'ru-RU',
            'en' => 'en-US',
        ];

        $organization = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => (string) config('seo.site_name'),
            'url' => $base,
            'description' => $description,
        ];

        $website = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => (string) config('seo.site_name'),
            'url' => $landingUrl,
            'inLanguage' => $languageMap[$locale] ?? $locale,
        ];

        $faqItems = collect(__('landing.faqs'))
            ->map(static fn (array $item): array => [
                '@type' => 'Question',
                'name' => $item['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $item['a'],
                ],
            ])
            ->values()
            ->all();

        $faq = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqItems,
        ];

        return [$organization, $website, $faq];
    }
}
