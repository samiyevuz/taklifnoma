@extends('layouts.admin')

@section('admin')
    <div class="mb-8">
        <p class="section-label mb-3">{{ __('admin.rsvps') }}</p>
        <h1 class="font-serif text-display font-semibold text-ink">{{ __('admin.rsvps_title') }}</h1>
    </div>

    <div class="admin-stat-grid mb-8">
        <div class="admin-stat glass-luxury"><p class="admin-stat__value">{{ $stats['total'] }}</p><p class="admin-stat__label">{{ __('admin.stats.rsvp_total') }}</p></div>
        <div class="admin-stat glass-luxury"><p class="admin-stat__value">{{ $stats['attending'] }}</p><p class="admin-stat__label">{{ __('admin.stats.rsvp_attending') }}</p></div>
        <div class="admin-stat glass-luxury"><p class="admin-stat__value">{{ $stats['declined'] }}</p><p class="admin-stat__label">{{ __('admin.stats.rsvp_declined') }}</p></div>
        <div class="admin-stat glass-luxury"><p class="admin-stat__value">{{ $stats['guests'] }}</p><p class="admin-stat__label">{{ __('admin.stats.rsvp_guests') }}</p></div>
    </div>

    <div class="admin-card glass-luxury">
        <form method="GET" class="admin-filters">
            <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('admin.table.guest') }}" class="admin-input min-w-[200px] flex-1">
            <select name="attending" class="admin-select">
                <option value="">{{ __('admin.filter_all') }}</option>
                <option value="1" @selected($attending === '1')>{{ __('admin.filter_attending') }}</option>
                <option value="0" @selected($attending === '0')>{{ __('admin.filter_declined') }}</option>
            </select>
            <button type="submit" class="btn-outline-luxury text-sm">{{ __('admin.search') }}</button>
        </form>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('admin.table.guest') }}</th>
                    <th>{{ __('admin.table.event') }}</th>
                    <th>{{ __('admin.owner') }}</th>
                    <th>{{ __('admin.created_at') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($responses as $response)
                    <tr>
                        <td>
                            <p class="font-medium text-ink">{{ $response->guest_name }}</p>
                            <p class="text-xs {{ $response->is_attending ? 'text-luxury-emerald' : 'text-ink-muted' }}">{{ $response->guestSummary() }}</p>
                        </td>
                        <td>
                            @if ($response->invitation)
                                <a href="{{ route('admin.invitations.show', $response->invitation) }}" class="text-sm font-medium text-ink hover:text-luxury-gold-dark">{{ $response->invitation->displayTitle() }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $response->invitation?->user?->name ?? '—' }}</td>
                        <td>{{ $response->created_at?->timezone('Asia/Tashkent')->format('d.m.Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-ink-muted py-8 text-center">{{ __('admin.no_results') }}</td></tr>
                @endforelse
            </tbody>
        </table>

        @if ($responses->hasPages())
            <div class="mt-6 pt-4 border-t border-white/30">{{ $responses->links() }}</div>
        @endif
    </div>
@endsection
