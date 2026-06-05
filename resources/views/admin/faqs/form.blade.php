@extends('layouts.admin')

@section('admin')
    <div class="mb-8">
        <a href="{{ route('admin.faqs.index') }}" class="text-sm font-semibold text-luxury-gold-dark hover:underline">← {{ __('admin.faqs') }}</a>
        <h1 class="mt-3 font-serif text-display font-semibold text-ink">{{ $title }}</h1>
    </div>

    <form method="POST" action="{{ $faq->exists ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}" class="admin-form-grid">
        @csrf
        @if ($faq->exists)
            @method('PUT')
        @endif

        <div class="admin-card glass-luxury">
            <h2 class="admin-card__title">{{ __('admin.sections.general') }}</h2>

            <div class="admin-form-row admin-form-row--2">
                <div>
                    <label class="admin-label" for="sort_order">{{ __('admin.table.sort') }}</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $faq->sort_order) }}" class="admin-input w-full" min="0" required>
                </div>
                <div class="flex items-end">
                    <label class="admin-check">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $faq->is_active ?? true))>
                        <span>{{ __('admin.filter_active') }}</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="admin-card glass-luxury">
            <h2 class="admin-card__title">{{ __('admin.sections.translations') }}</h2>

            @foreach ($locales as $code => $meta)
                @php $trans = old("translations.{$code}", $faq->translations[$code] ?? []); @endphp
                <div class="admin-locale-block">
                    <p class="admin-locale-block__title">{{ $meta['flag'] ?? '' }} {{ $meta['label'] ?? strtoupper($code) }}</p>

                    <div class="admin-form-row">
                        <label class="admin-label">{{ __('admin.table.question') }}</label>
                        <input type="text" name="translations[{{ $code }}][q]" value="{{ $trans['q'] ?? '' }}" class="admin-input w-full" @if($code === 'uz') required @endif>
                    </div>

                    <div class="admin-form-row">
                        <label class="admin-label">{{ __('admin.table.answer') }}</label>
                        <textarea name="translations[{{ $code }}][a]" rows="4" class="admin-input admin-textarea w-full" @if($code === 'uz') required @endif>{{ $trans['a'] ?? '' }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn-primary-luxury">{{ __('admin.save') }}</button>
            <a href="{{ route('admin.faqs.index') }}" class="btn-outline-luxury">{{ __('admin.cancel') }}</a>
        </div>
    </form>
@endsection
