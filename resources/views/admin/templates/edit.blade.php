@extends('layouts.admin')

@section('admin')
    <div class="mb-8">
        <a href="{{ route('admin.templates.index') }}" class="text-sm font-semibold text-luxury-gold-dark hover:underline">← {{ __('admin.templates') }}</a>
        <h1 class="mt-3 font-serif text-display font-semibold text-ink">{{ $template->localizedTitle('uz') }}</h1>
        <p class="mt-2 text-sm text-ink-soft">{{ __('admin.template_edit_subtitle') }}</p>
    </div>

    <form method="POST" action="{{ route('admin.templates.update', $template) }}" enctype="multipart/form-data" class="admin-form-grid">
        @csrf
        @method('PUT')

        <div class="admin-card glass-luxury">
            <h2 class="admin-card__title">{{ __('admin.sections.general') }}</h2>

            <div class="admin-form-row">
                <label class="admin-label">{{ __('admin.table.slug') }}</label>
                <input type="text" value="{{ $template->slug }}" class="admin-input w-full" disabled>
            </div>

            <div class="admin-form-row">
                <label class="admin-label" for="price_amount">{{ __('admin.table.amount') }} ({{ __('landing.currency') }})</label>
                <input type="number" id="price_amount" name="price_amount" value="{{ old('price_amount', $template->price_amount) }}" class="admin-input w-full" min="0" required>
            </div>

            <div class="admin-form-row admin-form-row--2">
                <div>
                    <label class="admin-label" for="sort_order">{{ __('admin.table.sort') }}</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $template->sort_order) }}" class="admin-input w-full" min="0" required>
                </div>
                <div class="flex items-end">
                    <label class="admin-check">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $template->is_active))>
                        <span>{{ __('admin.filter_active') }}</span>
                    </label>
                </div>
            </div>

            <div class="admin-form-row">
                <label class="admin-label" for="cover">{{ __('admin.table.cover') }}</label>
                @if ($template->coverUrl())
                    <img src="{{ $template->coverUrl() }}" alt="" class="admin-cover-preview mb-3">
                @endif
                <input type="file" id="cover" name="cover" accept="image/jpeg,image/png,image/webp" class="admin-input w-full">
                <p class="admin-hint">{{ __('admin.cover_hint') }}</p>
            </div>
        </div>

        <div class="admin-card glass-luxury">
            <h2 class="admin-card__title">{{ __('admin.sections.translations') }}</h2>

            @foreach ($locales as $code => $meta)
                @php $trans = old("translations.{$code}", $template->translations[$code] ?? []); @endphp
                <div class="admin-locale-block">
                    <p class="admin-locale-block__title">{{ $meta['flag'] ?? '' }} {{ $meta['label'] ?? strtoupper($code) }}</p>

                    <div class="admin-form-row">
                        <label class="admin-label">{{ __('admin.table.name') }}</label>
                        <input type="text" name="translations[{{ $code }}][title]" value="{{ $trans['title'] ?? '' }}" class="admin-input w-full" @if($code === 'uz') required @endif>
                    </div>

                    <div class="admin-form-row">
                        <label class="admin-label">{{ __('admin.table.description') }}</label>
                        <textarea name="translations[{{ $code }}][desc]" rows="3" class="admin-input admin-textarea w-full" @if($code === 'uz') required @endif>{{ $trans['desc'] ?? '' }}</textarea>
                    </div>

                    <div class="admin-form-row">
                        <label class="admin-label">{{ __('admin.table.badge') }}</label>
                        <input type="text" name="translations[{{ $code }}][badge]" value="{{ $trans['badge'] ?? '' }}" class="admin-input w-full" placeholder="{{ __('admin.badge_placeholder') }}">
                    </div>
                </div>
            @endforeach
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn-primary-luxury">{{ __('admin.save') }}</button>
            <a href="{{ route('admin.templates.index') }}" class="btn-outline-luxury">{{ __('admin.cancel') }}</a>
        </div>
    </form>
@endsection
