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

    <div class="admin-card glass-luxury mt-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="admin-card__title mb-1">{{ __('admin.sections.variants') }}</h2>
                <p class="text-sm text-ink-soft">{{ __('admin.variants_subtitle') }}</p>
            </div>
            <a href="{{ route('admin.templates.variants.create', $template) }}" class="btn-primary-luxury text-sm">
                + {{ __('admin.add_variant') }}
            </a>
        </div>

        @if ($template->variants->isEmpty())
            <p class="py-6 text-center text-sm text-ink-muted">{{ __('admin.no_results') }}</p>
        @else
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('admin.table.cover') }}</th>
                        <th>{{ __('admin.table.name') }}</th>
                        <th>{{ __('admin.table.amount') }}</th>
                        <th>{{ __('admin.table.plan_tier') }}</th>
                        <th>{{ __('admin.table.badge') }}</th>
                        <th>{{ __('admin.status') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($template->variants as $variant)
                        <tr>
                            <td>
                                @if ($variant->coverUrl())
                                    <img src="{{ $variant->coverUrl() }}" alt="" class="admin-thumb">
                                @else
                                    <span class="admin-thumb admin-thumb--empty">—</span>
                                @endif
                            </td>
                            <td>
                                <p class="font-medium text-ink">{{ $variant->title }}</p>
                                <p class="text-xs text-ink-muted"><code>{{ $variant->variant_key }}</code></p>
                                @if ($variant->is_default)
                                    <span class="mt-1 inline-block rounded-full bg-luxury-gold/15 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-luxury-gold-dark">
                                        {{ __('admin.variant_default') }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $variant->formattedPrice() }}</td>
                            <td>{{ $variant->themeLabel() }}</td>
                            <td>{{ $variant->badge ?? '—' }}</td>
                            <td>
                                <span class="admin-badge admin-badge--{{ $variant->is_active ? 'active' : 'expired' }}">
                                    {{ $variant->is_active ? __('admin.filter_active') : __('admin.inactive') }}
                                </span>
                            </td>
                            <td>
                                <div class="flex flex-wrap items-center gap-3">
                                    <a href="{{ route('admin.templates.variants.edit', [$template, $variant]) }}" class="text-sm font-semibold text-luxury-gold-dark hover:underline">
                                        {{ __('admin.edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('admin.templates.variants.destroy', [$template, $variant]) }}" onsubmit="return confirm(@json(__('admin.confirm_delete')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-red-600 hover:underline">
                                            {{ __('admin.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
