@extends('layouts.admin')

@section('admin')
    <div class="mb-8">
        <p class="section-label mb-3">{{ __('admin.invitations') }}</p>
        <h1 class="font-serif text-display font-semibold text-ink">{{ __('admin.invitations_title') }}</h1>
    </div>

    <div class="admin-card glass-luxury">
        <form method="GET" class="admin-filters">
            <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('admin.search_placeholder') }}" class="admin-input min-w-[200px] flex-1">
            <select name="status" class="admin-select">
                <option value="">{{ __('admin.filter_all') }}</option>
                <option value="active" @selected($status === 'active')>{{ __('admin.filter_active') }}</option>
                <option value="draft" @selected($status === 'draft')>{{ __('admin.filter_draft') }}</option>
                <option value="expired" @selected($status === 'expired')>{{ __('admin.filter_expired') }}</option>
            </select>
            <button type="submit" class="btn-outline-luxury text-sm">{{ __('admin.search') }}</button>
        </form>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('admin.table.event') }}</th>
                    <th>{{ __('admin.owner') }}</th>
                    <th>{{ __('admin.table.slug') }}</th>
                    <th>RSVP</th>
                    <th>{{ __('admin.status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invitations as $invitation)
                    <tr>
                        <td>
                            <p class="font-medium text-ink">{{ $invitation->displayTitle() }}</p>
                            <p class="text-xs text-ink-muted">{{ $invitation->event_type }} · {{ $invitation->created_at?->timezone('Asia/Tashkent')->format('d.m.Y') }}</p>
                        </td>
                        <td>{{ $invitation->user?->name ?? '—' }}</td>
                        <td><code class="text-xs">/l/{{ $invitation->slug }}</code></td>
                        <td>{{ $invitation->rsvp_responses_count }}</td>
                        <td><span class="admin-badge admin-badge--{{ $invitation->status }}">{{ $invitation->statusLabel() }}</span></td>
                        <td><a href="{{ route('admin.invitations.show', $invitation) }}" class="text-sm font-semibold text-luxury-gold-dark hover:underline">{{ __('admin.view') }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-ink-muted py-8 text-center">{{ __('admin.no_results') }}</td></tr>
                @endforelse
            </tbody>
        </table>

        @if ($invitations->hasPages())
            <div class="mt-6 pt-4 border-t border-white/30">{{ $invitations->links() }}</div>
        @endif
    </div>
@endsection
