<?php

namespace App\Console\Commands;

use App\Support\LocaleManager;
use App\Support\TemplateCatalog;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate sitemap.xml for indexable public pages';

    public function handle(): int
    {
        $sitemap = Sitemap::create();

        foreach (LocaleManager::codes() as $locale) {
            $sitemap->add(
                Url::create($this->sitemapUrl($locale))
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(1.0)
            );

            foreach (TemplateCatalog::definitions() as $template) {
                $sitemap->add(
                    Url::create($this->sitemapUrl($locale, 'preview/'.$template['slug']))
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.8)
                );
            }
        }

        $path = public_path('sitemap.xml');
        $sitemap->writeToFile($path);

        $this->info("Sitemap written to {$path}");

        return self::SUCCESS;
    }

    private function sitemapUrl(string $locale, string $pathAfterLocale = ''): string
    {
        $base = rtrim((string) config('seo.site_url'), '/');
        $pathAfterLocale = trim($pathAfterLocale, '/');

        if ($pathAfterLocale === '') {
            return $base.'/'.$locale.'/';
        }

        return $base.'/'.$locale.'/'.$pathAfterLocale;
    }
}
