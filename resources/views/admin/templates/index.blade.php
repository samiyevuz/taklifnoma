@extends('layouts.admin')

@section('admin')
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="section-label mb-3">{{ __('admin.templates') }}</p>
            <h1 class="font-serif text-display font-semibold text-ink">{{ __('admin.templates_title') }}</h1>
            <p class="mt-2 text-sm text-ink-soft">{{ __('admin.templates_subtitle') }}</p>
        </div>
    </div>

    <div class="admin-card glass-luxury">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('admin.table.cover') }}</th>
                    <th>{{ __('admin.table.name') }}</th>
                    <th>{{ __('admin.table.amount') }}</th>
                    <th>{{ __('admin.table.variants') }}</th>
                    <th>{{ __('admin.table.badge') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($templates as $template)
                    <tr>
                        <td>
                            @if ($template->coverUrl())
                                <img src="{{ $template->coverUrl() }}" alt="" class="admin-thumb">
                            @else
                                <span class="admin-thumb admin-thumb--empty">—</span>
                            @endif
                        </td>
                        <td>
                            <p class="font-medium text-ink">{{ $template->localizedTitle('uz') }}</p>
                            <p class="text-xs text-ink-muted"><code>{{ $template->slug }}</code></p>
                        </td>
                        <td>{{ $template->formattedPrice() }}</td>
                        <td>
                            <span class="admin-badge admin-badge--active">{{ $template->variants_count }}</span>
                        </td>
                        <td>{{ $template->localizedBadge('uz') ?? '—' }}</td>
                        <td>
                            <span class="admin-badge admin-badge--{{ $template->is_active ? 'active' : 'expired' }}">
                                {{ $template->is_active ? __('admin.filter_active') : __('admin.inactive') }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.templates.edit', $template) }}" class="text-sm font-semibold text-luxury-gold-dark hover:underline">
                                {{ __('admin.edit') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-ink-muted">{{ __('admin.no_results') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
