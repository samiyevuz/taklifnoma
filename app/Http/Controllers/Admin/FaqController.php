<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingFaq;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        return view('admin.faqs.index', [
            'title' => __('admin.faqs_title'),
            'faqs' => LandingFaq::query()->ordered()->get(),
            'faqMeta' => $this->faqMetaForForm(),
            'locales' => config('locales.supported', []),
        ]);
    }

    public function create(): View
    {
        return view('admin.faqs.form', [
            'title' => __('admin.faq_create'),
            'faq' => new LandingFaq([
                'sort_order' => (int) LandingFaq::query()->max('sort_order') + 1,
                'is_active' => true,
                'translations' => [],
            ]),
            'locales' => config('locales.supported', []),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateFaq($request);

        LandingFaq::query()->create([
            'translations' => $this->normalizeTranslations($validated['translations']),
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        SiteSetting::clearCache();

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', __('admin.faq_created'));
    }

    public function edit(LandingFaq $faq): View
    {
        return view('admin.faqs.form', [
            'title' => __('admin.faq_edit'),
            'faq' => $faq,
            'locales' => config('locales.supported', []),
        ]);
    }

    public function update(Request $request, LandingFaq $faq): RedirectResponse
    {
        $validated = $this->validateFaq($request);

        $faq->update([
            'translations' => $this->normalizeTranslations($validated['translations']),
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->boolean('is_active'),
        ]);

        SiteSetting::clearCache();

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', __('admin.faq_updated'));
    }

    public function destroy(LandingFaq $faq): RedirectResponse
    {
        $faq->delete();

        SiteSetting::clearCache();

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', __('admin.faq_deleted'));
    }

    public function updateMeta(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'faq_meta' => ['required', 'array'],
            'faq_meta.uz.label' => ['required', 'string', 'max:80'],
            'faq_meta.uz.title' => ['required', 'string', 'max:160'],
            'faq_meta.uz.desc' => ['required', 'string', 'max:300'],
            'faq_meta.en.label' => ['nullable', 'string', 'max:80'],
            'faq_meta.en.title' => ['nullable', 'string', 'max:160'],
            'faq_meta.en.desc' => ['nullable', 'string', 'max:300'],
            'faq_meta.ru.label' => ['nullable', 'string', 'max:80'],
            'faq_meta.ru.title' => ['nullable', 'string', 'max:160'],
            'faq_meta.ru.desc' => ['nullable', 'string', 'max:300'],
        ]);

        foreach (['uz', 'en', 'ru'] as $locale) {
            $meta = $validated['faq_meta'][$locale] ?? [];

            foreach (['label', 'title', 'desc'] as $field) {
                $value = trim((string) ($meta[$field] ?? ''));
                SiteSetting::setValue("faq.{$locale}.{$field}", $value !== '' ? $value : null);
            }
        }

        return back()->with('success', __('admin.faq_meta_updated'));
    }

    private function validateFaq(Request $request): array
    {
        return $request->validate([
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'is_active' => ['sometimes', 'boolean'],
            'translations' => ['required', 'array'],
            'translations.uz.q' => ['required', 'string', 'max:300'],
            'translations.uz.a' => ['required', 'string', 'max:2000'],
            'translations.en.q' => ['nullable', 'string', 'max:300'],
            'translations.en.a' => ['nullable', 'string', 'max:2000'],
            'translations.ru.q' => ['nullable', 'string', 'max:300'],
            'translations.ru.a' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function normalizeTranslations(array $translations): array
    {
        $normalized = [];

        foreach (['uz', 'en', 'ru'] as $locale) {
            $item = $translations[$locale] ?? [];
            $normalized[$locale] = [
                'q' => trim((string) ($item['q'] ?? '')),
                'a' => trim((string) ($item['a'] ?? '')),
            ];
        }

        return $normalized;
    }

    private function faqMetaForForm(): array
    {
        $meta = [];

        foreach (['uz', 'en', 'ru'] as $locale) {
            $meta[$locale] = [
                'label' => SiteSetting::getValue("faq.{$locale}.label") ?? __('landing.faq_label', [], $locale),
                'title' => SiteSetting::getValue("faq.{$locale}.title") ?? __('landing.faq_title', [], $locale),
                'desc' => SiteSetting::getValue("faq.{$locale}.desc") ?? __('landing.faq_desc', [], $locale),
            ];
        }

        return $meta;
    }
}
