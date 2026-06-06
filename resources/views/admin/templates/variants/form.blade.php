@extends('layouts.admin')

@section('admin')
    <div class="mb-8">
        <a href="{{ route('admin.templates.edit', $template) }}" class="text-sm font-semibold text-luxury-gold-dark hover:underline">← {{ $template->localizedTitle('uz') }}</a>
        <h1 class="mt-3 font-serif text-display font-semibold text-ink">{{ $title }}</h1>
        <p class="mt-2 text-sm text-ink-soft">{{ __('admin.variant_form_subtitle') }}</p>
    </div>

    <form method="POST"
          action="{{ $variant->exists ? route('admin.templates.variants.update', [$template, $variant]) : route('admin.templates.variants.store', $template) }}"
          enctype="multipart/form-data"
          class="admin-form-grid">
        @csrf
        @if ($variant->exists)
            @method('PUT')
        @endif

        <div class="admin-card glass-luxury">
            <h2 class="admin-card__title">{{ __('admin.sections.general') }}</h2>

            @if ($variant->exists)
                <div class="admin-form-row">
                    <label class="admin-label">{{ __('admin.table.variant_key') }}</label>
                    <input type="text" value="{{ $variant->variant_key }}" class="admin-input w-full font-mono text-sm" disabled>
                    <p class="admin-hint">{{ __('admin.variant_key_hint') }}</p>
                </div>
            @else
                <div class="admin-form-row">
                    <label class="admin-label" for="variant_key">{{ __('admin.table.variant_key') }}</label>
                    <input type="text" id="variant_key" name="variant_key" value="{{ old('variant_key') }}" class="admin-input w-full font-mono text-sm" placeholder="nikoh-premium">
                    <p class="admin-hint">{{ __('admin.variant_key_create_hint') }}</p>
                </div>
            @endif

            <div class="admin-form-row">
                <label class="admin-label" for="title">{{ __('admin.table.name') }}</label>
                <input type="text" id="title" name="title" value="{{ old('title', $variant->title) }}" class="admin-input w-full" required>
            </div>

            <div class="admin-form-row">
                <label class="admin-label" for="subtitle">{{ __('admin.table.description') }}</label>
                <input type="text" id="subtitle" name="subtitle" value="{{ old('subtitle', $variant->subtitle) }}" class="admin-input w-full">
            </div>

            <div class="admin-form-row admin-form-row--2">
                <div>
                    <label class="admin-label" for="price_amount">{{ __('admin.table.amount') }} ({{ __('landing.currency') }})</label>
                    <input type="number" id="price_amount" name="price_amount" value="{{ old('price_amount', $variant->price_amount) }}" class="admin-input w-full" min="0" required>
                </div>
                <div>
                    <label class="admin-label" for="theme">{{ __('admin.table.plan_tier') }}</label>
                    <select id="theme" name="theme" class="admin-input w-full" required>
                        @foreach ($themes as $theme)
                            <option value="{{ $theme }}" @selected(old('theme', $variant->theme) === $theme)>
                                {{ __("admin.themes.{$theme}") }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="admin-form-row admin-form-row--2">
                <div>
                    <label class="admin-label" for="guest_limit">{{ __('admin.table.guest_limit') }}</label>
                    <input type="number" id="guest_limit" name="guest_limit" value="{{ old('guest_limit', $variant->guest_limit) }}" class="admin-input w-full" min="1" placeholder="{{ __('admin.guest_limit_placeholder') }}">
                </div>
                <div>
                    <label class="admin-label" for="badge">{{ __('admin.table.badge') }}</label>
                    <input type="text" id="badge" name="badge" value="{{ old('badge', $variant->badge) }}" class="admin-input w-full" placeholder="{{ __('admin.badge_placeholder') }}">
                </div>
            </div>

            <div class="admin-form-row admin-form-row--2">
                <div>
                    <label class="admin-label" for="blade">{{ __('admin.table.blade') }}</label>
                    <input type="text" id="blade" name="blade" value="{{ old('blade', $variant->blade ?? $template->blade) }}" class="admin-input w-full font-mono text-sm">
                    <p class="admin-hint">{{ __('admin.blade_hint') }}</p>
                </div>
                <div>
                    <label class="admin-label" for="sort_order">{{ __('admin.table.sort') }}</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $variant->sort_order) }}" class="admin-input w-full" min="0" required>
                </div>
            </div>

            <div class="admin-form-row">
                <label class="admin-label" for="cover">{{ __('admin.table.cover') }}</label>
                @if ($variant->coverUrl())
                    <img src="{{ $variant->coverUrl() }}" alt="" class="admin-cover-preview mb-3">
                @endif
                <input type="file" id="cover" name="cover" accept="image/jpeg,image/png,image/webp" class="admin-input w-full">
                <p class="admin-hint">{{ __('admin.variant_cover_hint') }}</p>
            </div>

            <div class="admin-form-row flex flex-wrap gap-6">
                <label class="admin-check">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $variant->is_active ?? true))>
                    <span>{{ __('admin.filter_active') }}</span>
                </label>
                <label class="admin-check">
                    <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $variant->is_default))>
                    <span>{{ __('admin.variant_default') }}</span>
                </label>
            </div>
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn-primary-luxury">{{ __('admin.save') }}</button>
            <a href="{{ route('admin.templates.edit', $template) }}" class="btn-outline-luxury">{{ __('admin.cancel') }}</a>
        </div>
    </form>
@endsection
