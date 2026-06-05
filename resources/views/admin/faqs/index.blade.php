@extends('layouts.admin')

@section('admin')
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="section-label mb-3">{{ __('admin.faqs') }}</p>
            <h1 class="font-serif text-display font-semibold text-ink">{{ __('admin.faqs_title') }}</h1>
            <p class="mt-2 text-sm text-ink-soft">{{ __('admin.faqs_subtitle') }}</p>
        </div>
        <a href="{{ route('admin.faqs.create') }}" class="btn-primary-luxury text-sm">{{ __('admin.faq_create') }}</a>
    </div>

    <div class="admin-card glass-luxury mb-8">
        <h2 class="admin-card__title">{{ __('admin.faq_section_settings') }}</h2>
        <form method="POST" action="{{ route('admin.faqs.meta') }}" class="admin-form-grid">
            @csrf
            @method('PUT')

            @foreach ($locales as $code => $meta)
                @php $section = old("faq_meta.{$code}", $faqMeta[$code] ?? []); @endphp
                <div class="admin-locale-block">
                    <p class="admin-locale-block__title">{{ $meta['flag'] ?? '' }} {{ $meta['label'] ?? strtoupper($code) }}</p>
                    <div class="admin-form-row">
                        <label class="admin-label">{{ __('admin.faq_section_label') }}</label>
                        <input type="text" name="faq_meta[{{ $code }}][label]" value="{{ $section['label'] ?? '' }}" class="admin-input w-full" @if($code === 'uz') required @endif>
                    </div>
                    <div class="admin-form-row">
                        <label class="admin-label">{{ __('admin.faq_section_title') }}</label>
                        <input type="text" name="faq_meta[{{ $code }}][title]" value="{{ $section['title'] ?? '' }}" class="admin-input w-full" @if($code === 'uz') required @endif>
                    </div>
                    <div class="admin-form-row">
                        <label class="admin-label">{{ __('admin.faq_section_desc') }}</label>
                        <textarea name="faq_meta[{{ $code }}][desc]" rows="2" class="admin-input admin-textarea w-full" @if($code === 'uz') required @endif>{{ $section['desc'] ?? '' }}</textarea>
                    </div>
                </div>
            @endforeach

            <div class="admin-form-actions">
                <button type="submit" class="btn-outline-luxury text-sm">{{ __('admin.save_section') }}</button>
            </div>
        </form>
    </div>

    <div class="admin-card glass-luxury">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('admin.table.question') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($faqs as $faq)
                    <tr>
                        <td>{{ $faq->sort_order }}</td>
                        <td>
                            <p class="font-medium text-ink">{{ $faq->localizedQuestion('uz') }}</p>
                            <p class="text-xs text-ink-muted line-clamp-1">{{ $faq->localizedAnswer('uz') }}</p>
                        </td>
                        <td>
                            <span class="admin-badge admin-badge--{{ $faq->is_active ? 'active' : 'expired' }}">
                                {{ $faq->is_active ? __('admin.filter_active') : __('admin.inactive') }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap">
                            <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-sm font-semibold text-luxury-gold-dark hover:underline">{{ __('admin.edit') }}</a>
                            <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" class="inline ml-3" onsubmit="return confirm(@json(__('admin.confirm_delete')))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-red-600 hover:underline">{{ __('admin.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-ink-muted">{{ __('admin.no_results') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
