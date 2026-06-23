<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_to_default_locale_landing(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/uz');
    }

    public function test_localized_landing_returns_successful_response(): void
    {
        $response = $this->get('/uz');

        $response->assertOk();
    }

    public function test_legacy_login_redirects_to_localized_url(): void
    {
        $response = $this->get('/login');

        $response->assertRedirect('/uz/login');
    }

    public function test_locale_switch_preserves_path(): void
    {
        $response = $this->get('/uz/login');

        $response->assertOk();
        $this->assertStringContainsString('/ru/login', \App\Support\LocaleManager::switchUrl('ru'));
    }
}
