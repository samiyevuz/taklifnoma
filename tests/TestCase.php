<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $buildDir = public_path('build');

        if (! is_dir($buildDir)) {
            mkdir($buildDir, 0777, true);
        }

        $manifest = $buildDir.'/manifest.json';

        file_put_contents($manifest, json_encode([
                'resources/css/app.css' => [
                    'file' => 'assets/app.css',
                    'src' => 'resources/css/app.css',
                    'isEntry' => true,
                ],
                'resources/js/app.js' => [
                    'file' => 'assets/app.js',
                    'src' => 'resources/js/app.js',
                    'isEntry' => true,
                ],
                'resources/js/invitation.js' => [
                    'file' => 'assets/invitation.js',
                    'src' => 'resources/js/invitation.js',
                    'isEntry' => true,
                ],
                'resources/js/builder.js' => [
                    'file' => 'assets/builder.js',
                    'src' => 'resources/js/builder.js',
                    'isEntry' => true,
                ],
                'resources/js/admin.js' => [
                    'file' => 'assets/admin.js',
                    'src' => 'resources/js/admin.js',
                    'isEntry' => true,
                ],
            ]));
    }
}
