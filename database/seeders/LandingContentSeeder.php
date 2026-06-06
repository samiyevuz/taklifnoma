<?php

namespace Database\Seeders;

use App\Models\EventTemplate;
use App\Models\EventTemplateVariant;
use App\Models\LandingFaq;
use App\Models\SiteSetting;
use App\Support\TemplateCatalog;
use App\Support\TemplateVariantCatalog;
use Illuminate\Database\Seeder;

class LandingContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTemplates();
        $this->seedVariants();
        $this->seedFaqs();
        $this->seedContact();
        $this->seedFaqMeta();

        SiteSetting::clearCache();
    }

    private function seedTemplates(): void
    {
        foreach (TemplateCatalog::fallbackDefinitions() as $index => $definition) {
            $slug = $definition['slug'];
            $translations = [];

            foreach (['uz', 'en', 'ru'] as $locale) {
                $trans = __("landing.templates.{$slug}", [], $locale);

                $translations[$locale] = [
                    'title' => is_array($trans) ? ($trans['title'] ?? $slug) : $slug,
                    'desc' => is_array($trans) ? ($trans['desc'] ?? '') : '',
                    'badge' => is_array($trans) ? ($trans['tag'] ?? null) : null,
                ];
            }

            EventTemplate::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'blade' => $definition['template'],
                    'visual' => $definition['visual'],
                    'cover_path' => $definition['cover_image'] ?? null,
                    'price_amount' => $definition['price_amount'],
                    'preview_route' => $definition['preview_route'],
                    'preview_param' => $definition['preview_param'],
                    'translations' => $translations,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
        TemplateCatalog::clearCache();
    }

    private function seedVariants(): void
    {
        foreach (TemplateVariantCatalog::staticDefinitions() as $slug => $variants) {
            $template = EventTemplate::query()->where('slug', $slug)->first();

            if (! $template) {
                continue;
            }

            $defaultKey = null;

            foreach ($variants as $variant) {
                if (($variant['badge'] ?? null) === 'Eng mashhur') {
                    $defaultKey = $variant['id'];
                    break;
                }
            }

            if ($defaultKey === null) {
                foreach ($variants as $variant) {
                    if (($variant['theme'] ?? '') === 'premium') {
                        $defaultKey = $variant['id'];
                        break;
                    }
                }
            }

            foreach ($variants as $index => $variant) {
                EventTemplateVariant::query()->updateOrCreate(
                    ['variant_key' => $variant['id']],
                    [
                        'event_template_id' => $template->id,
                        'title' => $variant['title'],
                        'subtitle' => $variant['subtitle'] ?? null,
                        'price_amount' => $variant['price_amount'],
                        'theme' => $variant['theme'] ?? 'premium',
                        'blade' => $variant['blade'] ?? null,
                        'cover_path' => null,
                        'badge' => $variant['badge'] ?? null,
                        'guest_limit' => null,
                        'sort_order' => $index + 1,
                        'is_default' => $variant['id'] === $defaultKey,
                        'is_active' => true,
                    ]
                );
            }
        }

        TemplateVariantCatalog::clearCache();
    }

    private function seedFaqs(): void
    {
        foreach (['uz', 'en', 'ru'] as $locale) {
            $items = __('landing.faqs', [], $locale);

            if (! is_array($items)) {
                continue;
            }

            foreach ($items as $index => $item) {
                $faq = LandingFaq::query()->where('sort_order', $index + 1)->first();

                $translations = $faq?->translations ?? [];

                $translations[$locale] = [
                    'q' => $item['q'] ?? '',
                    'a' => $item['a'] ?? '',
                ];

                LandingFaq::query()->updateOrCreate(
                    ['sort_order' => $index + 1],
                    [
                        'translations' => $translations,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    private function seedContact(): void
    {
        $pairs = [];

        foreach (['email', 'phone', 'instagram', 'telegram', 'youtube', 'facebook', 'whatsapp'] as $key) {
            $pairs["contact.{$key}"] = config("social.{$key}");
        }

        SiteSetting::setMany($pairs);
    }

    private function seedFaqMeta(): void
    {
        $pairs = [];

        foreach (['uz', 'en', 'ru'] as $locale) {
            $pairs["faq.{$locale}.label"] = __('landing.faq_label', [], $locale);
            $pairs["faq.{$locale}.title"] = __('landing.faq_title', [], $locale);
            $pairs["faq.{$locale}.desc"] = __('landing.faq_desc', [], $locale);
        }

        SiteSetting::setMany($pairs);
    }
}
