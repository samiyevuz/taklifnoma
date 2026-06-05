<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventTemplate;
use App\Models\SiteSetting;
use App\Support\TemplateCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(): View
    {
        return view('admin.templates.index', [
            'title' => __('admin.templates_title'),
            'templates' => EventTemplate::query()->ordered()->get(),
        ]);
    }

    public function edit(EventTemplate $eventTemplate): View
    {
        return view('admin.templates.edit', [
            'title' => $eventTemplate->localizedTitle('uz').' — '.__('admin.templates_title'),
            'template' => $eventTemplate,
            'locales' => config('locales.supported', []),
        ]);
    }

    public function update(Request $request, EventTemplate $eventTemplate): RedirectResponse
    {
        $validated = $request->validate([
            'price_amount' => ['required', 'integer', 'min:0', 'max:99999999'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'translations' => ['required', 'array'],
            'translations.uz.title' => ['required', 'string', 'max:120'],
            'translations.uz.desc' => ['required', 'string', 'max:500'],
            'translations.uz.badge' => ['nullable', 'string', 'max:40'],
            'translations.en.title' => ['nullable', 'string', 'max:120'],
            'translations.en.desc' => ['nullable', 'string', 'max:500'],
            'translations.en.badge' => ['nullable', 'string', 'max:40'],
            'translations.ru.title' => ['nullable', 'string', 'max:120'],
            'translations.ru.desc' => ['nullable', 'string', 'max:500'],
            'translations.ru.badge' => ['nullable', 'string', 'max:40'],
        ]);

        $payload = [
            'price_amount' => $validated['price_amount'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->boolean('is_active'),
            'translations' => $this->normalizeTranslations($validated['translations']),
        ];

        if ($request->hasFile('cover')) {
            $directory = public_path('images/templates');
            File::ensureDirectoryExists($directory);

            $extension = $request->file('cover')->getClientOriginalExtension() ?: 'jpg';
            $filename = $eventTemplate->slug.'.'.$extension;

            $request->file('cover')->move($directory, $filename);

            $payload['cover_path'] = 'images/templates/'.$filename;
        }

        $eventTemplate->update($payload);

        TemplateCatalog::clearCache();
        SiteSetting::clearCache();

        return redirect()
            ->route('admin.templates.index')
            ->with('success', __('admin.template_updated'));
    }

    private function normalizeTranslations(array $translations): array
    {
        $normalized = [];

        foreach (['uz', 'en', 'ru'] as $locale) {
            $item = $translations[$locale] ?? [];
            $normalized[$locale] = [
                'title' => trim((string) ($item['title'] ?? '')),
                'desc' => trim((string) ($item['desc'] ?? '')),
                'badge' => filled($item['badge'] ?? null) ? trim((string) $item['badge']) : null,
            ];
        }

        return $normalized;
    }
}
