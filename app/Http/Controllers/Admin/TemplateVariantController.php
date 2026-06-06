<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventTemplate;
use App\Models\EventTemplateVariant;
use App\Models\Invitation;
use App\Support\TemplateVariantCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TemplateVariantController extends Controller
{
    public function create(EventTemplate $eventTemplate): View
    {
        return view('admin.templates.variants.form', [
            'title' => __('admin.variant_create').' — '.$eventTemplate->localizedTitle('uz'),
            'template' => $eventTemplate,
            'variant' => new EventTemplateVariant([
                'sort_order' => (int) $eventTemplate->variants()->max('sort_order') + 1,
                'theme' => 'premium',
                'is_active' => true,
            ]),
            'themes' => EventTemplateVariant::THEMES,
        ]);
    }

    public function store(Request $request, EventTemplate $eventTemplate): RedirectResponse
    {
        $validated = $this->validateVariant($request);

        $variantKey = $this->resolveVariantKey(
            $eventTemplate,
            $validated['variant_key'] ?? null,
            $validated['title']
        );

        $variant = $eventTemplate->variants()->create([
            'variant_key' => $variantKey,
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'price_amount' => $validated['price_amount'],
            'theme' => $validated['theme'],
            'blade' => $validated['blade'] ?? null,
            'badge' => $validated['badge'] ?? null,
            'guest_limit' => $validated['guest_limit'] ?? null,
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default'),
        ]);

        if ($request->hasFile('cover')) {
            $variant->update(['cover_path' => $this->storeCover($request, $eventTemplate, $variantKey)]);
        }

        $this->syncDefaultVariant($eventTemplate, $variant, $request->boolean('is_default'));

        TemplateVariantCatalog::clearCache();

        return redirect()
            ->route('admin.templates.edit', $eventTemplate)
            ->with('success', __('admin.variant_created'));
    }

    public function edit(EventTemplate $eventTemplate, EventTemplateVariant $variant): View
    {
        $this->ensureVariantBelongsToTemplate($eventTemplate, $variant);

        return view('admin.templates.variants.form', [
            'title' => __('admin.variant_edit').' — '.$variant->title,
            'template' => $eventTemplate,
            'variant' => $variant,
            'themes' => EventTemplateVariant::THEMES,
        ]);
    }

    public function update(Request $request, EventTemplate $eventTemplate, EventTemplateVariant $variant): RedirectResponse
    {
        $this->ensureVariantBelongsToTemplate($eventTemplate, $variant);

        $validated = $this->validateVariant($request, $variant);

        $payload = [
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'price_amount' => $validated['price_amount'],
            'theme' => $validated['theme'],
            'blade' => $validated['blade'] ?? null,
            'badge' => $validated['badge'] ?? null,
            'guest_limit' => $validated['guest_limit'] ?? null,
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->boolean('is_active'),
            'is_default' => $request->boolean('is_default'),
        ];

        if ($request->hasFile('cover')) {
            $payload['cover_path'] = $this->storeCover($request, $eventTemplate, $variant->variant_key);
        }

        $variant->update($payload);

        $this->syncDefaultVariant($eventTemplate, $variant, $request->boolean('is_default'));

        TemplateVariantCatalog::clearCache();

        return redirect()
            ->route('admin.templates.edit', $eventTemplate)
            ->with('success', __('admin.variant_updated'));
    }

    public function destroy(EventTemplate $eventTemplate, EventTemplateVariant $variant): RedirectResponse
    {
        $this->ensureVariantBelongsToTemplate($eventTemplate, $variant);

        if (Invitation::query()->where('template_variant', $variant->variant_key)->exists()) {
            return back()->with('error', __('admin.variant_in_use'));
        }

        $variant->delete();

        TemplateVariantCatalog::clearCache();

        return redirect()
            ->route('admin.templates.edit', $eventTemplate)
            ->with('success', __('admin.variant_deleted'));
    }

    private function validateVariant(Request $request, ?EventTemplateVariant $variant = null): array
    {
        return $request->validate([
            'variant_key' => [
                'nullable',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('event_template_variants', 'variant_key')->ignore($variant?->id),
            ],
            'title' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'price_amount' => ['required', 'integer', 'min:0', 'max:99999999'],
            'theme' => ['required', Rule::in(EventTemplateVariant::THEMES)],
            'blade' => ['nullable', 'string', 'max:80'],
            'badge' => ['nullable', 'string', 'max:40'],
            'guest_limit' => ['nullable', 'integer', 'min:1', 'max:99999'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'is_active' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);
    }

    private function resolveVariantKey(EventTemplate $template, ?string $requested, string $title): string
    {
        $base = filled($requested)
            ? Str::slug($requested)
            : Str::slug($template->slug.'-'.Str::slug($title));

        $key = $base;
        $suffix = 2;

        while (EventTemplateVariant::query()->where('variant_key', $key)->exists()) {
            $key = "{$base}-{$suffix}";
            $suffix++;
        }

        return $key;
    }

    private function storeCover(Request $request, EventTemplate $template, string $variantKey): string
    {
        $directory = public_path('images/templates/variants');
        File::ensureDirectoryExists($directory);

        $extension = $request->file('cover')->getClientOriginalExtension() ?: 'jpg';
        $filename = $variantKey.'.'.$extension;

        $request->file('cover')->move($directory, $filename);

        return 'images/templates/variants/'.$filename;
    }

    private function syncDefaultVariant(EventTemplate $template, EventTemplateVariant $variant, bool $isDefault): void
    {
        if (! $isDefault) {
            if (! $template->variants()->where('is_default', true)->exists()) {
                $fallback = $template->variants()->active()->ordered()->first();
                $fallback?->update(['is_default' => true]);
            }

            return;
        }

        $template->variants()
            ->where('id', '!=', $variant->id)
            ->update(['is_default' => false]);

        $variant->update(['is_default' => true]);
    }

    private function ensureVariantBelongsToTemplate(EventTemplate $template, EventTemplateVariant $variant): void
    {
        abort_unless($variant->event_template_id === $template->id, 404);
    }
}
