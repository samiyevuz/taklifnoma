<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\InvitationSeeder;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SeoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'https://taklifnoma.net',
            'seo.site_url' => 'https://taklifnoma.net',
        ]);

        \Illuminate\Support\Facades\URL::forceRootUrl('https://taklifnoma.net');
    }

    public function test_landing_page_is_indexable_with_hreflang_and_json_ld(): void
    {
        $response = $this->get('/uz');

        $response->assertOk();
        $response->assertSee('<link rel="canonical" href="https://taklifnoma.net/uz/"', false);
        $response->assertSee('hreflang="uz"', false);
        $response->assertSee('hreflang="ru"', false);
        $response->assertSee('hreflang="en"', false);
        $response->assertSee('hreflang="x-default"', false);
        $response->assertDontSee('noindex', false);
        $response->assertSee('"@type":"Organization"', false);
        $response->assertSee('"@type":"WebSite"', false);
        $response->assertSee('"@type":"FAQPage"', false);
    }

    public function test_preview_page_is_indexable_with_hreflang(): void
    {
        $response = $this->get('/uz/preview/nikoh');

        $response->assertOk();
        $response->assertSee('<link rel="canonical" href="https://taklifnoma.net/uz/preview/nikoh"', false);
        $response->assertSee('hreflang="uz"', false);
        $response->assertSee('hreflang="ru"', false);
        $response->assertSee('hreflang="en"', false);
        $response->assertDontSee('noindex', false);
    }

    public function test_invitation_long_url_is_noindex_with_canonical(): void
    {
        $this->seed(InvitationSeeder::class);

        $response = $this->get('/uz/l/farhod-shirin');

        $response->assertOk();
        $response->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        $response->assertSee('<link rel="canonical" href="https://taklifnoma.net/uz/l/farhod-shirin"', false);
        $response->assertSee('property="og:title"', false);
        $response->assertSee('property="og:description"', false);
    }

    public function test_invitation_short_url_uses_long_canonical(): void
    {
        $this->seed(InvitationSeeder::class);

        $response = $this->get('/uz/i/farhod-shirin');

        $response->assertOk();
        $response->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        $response->assertSee('<link rel="canonical" href="https://taklifnoma.net/uz/l/farhod-shirin"', false);
        $response->assertDontSee('canonical" href="https://taklifnoma.net/uz/i/farhod-shirin"', false);
    }

    public function test_invitation_meta_description_is_locale_aware(): void
    {
        $this->seed(InvitationSeeder::class);

        $response = $this->get('/en/l/farhod-shirin');

        $response->assertOk();
        $response->assertSee('meta name="description" content="Farhod &amp; Shirin', false);
        $response->assertSee('invitation"', false);
        $response->assertDontSee('taklifnomasi"', false);
    }

    public function test_login_page_is_noindex(): void
    {
        $response = $this->get('/uz/login');

        $response->assertOk();
        $response->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_builder_create_is_noindex(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/uz/builder/create');

        $response->assertOk();
        $response->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_robots_txt_disallows_private_paths(): void
    {
        $contents = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Disallow: /*/admin/', $contents);
        $this->assertStringContainsString('Disallow: /*/account/', $contents);
        $this->assertStringContainsString('Disallow: /*/builder/', $contents);
        $this->assertStringContainsString('Disallow: /*/login', $contents);
        $this->assertStringContainsString('Disallow: /*/l/', $contents);
        $this->assertStringContainsString('Sitemap: https://taklifnoma.net/sitemap.xml', $contents);
    }

    public function test_generate_sitemap_command_creates_public_file(): void
    {
        $path = public_path('sitemap.xml');

        if (is_file($path)) {
            unlink($path);
        }

        Artisan::call('sitemap:generate');

        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        $this->assertStringContainsString('https://taklifnoma.net/uz/', $contents);
        $this->assertStringContainsString('https://taklifnoma.net/en/preview/nikoh', $contents);
    }

    public function test_sitemap_route_serves_generated_file(): void
    {
        Artisan::call('sitemap:generate');

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/xml');
        $this->assertStringContainsString(
            'https://taklifnoma.net/ru/preview/nikoh',
            file_get_contents(public_path('sitemap.xml'))
        );
    }
}
