@extends('layouts.account')

@section('account')
    <div class="mb-8">
        <p class="section-label mb-3">{{ __('account.cabinet') }}</p>
        <h1 class="font-serif text-display font-semibold text-ink">{{ __('account.dashboard_welcome', ['name' => auth()->user()->name]) }}</h1>
        <p class="mt-2 text-ink-soft">{{ __('account.dashboard_subtitle') }}</p>
    </div>

    @if ($liveInvitation && $liveInvitation->isPublished())
        <div class="mb-8">
            <x-invitation.share-bar :invitation="$liveInvitation" />
        </div>
    @endif

    @if ($liveInvitation && $rsvpSnapshot)
        <div class="mb-8">
            <x-rsvp.live-panel
                :invitation="$liveInvitation"
                :snapshot="$rsvpSnapshot"
                :poll-url="route('builder.rsvp.live', $liveInvitation)"
                :compact="true"
            />
        </div>
    @endif

    <div class="account-stat-grid mb-8">
        <div class="account-stat glass-luxury"><p class="account-stat__value">{{ $stats['orders_total'] }}</p><p class="account-stat__label">{{ __('account.stats_orders') }}</p></div>
        <div class="account-stat glass-luxury"><p class="account-stat__value">{{ $stats['orders_published'] }}</p><p class="account-stat__label">{{ __('account.stats_published') }}</p></div>
        <div class="account-stat glass-luxury"><p class="account-stat__value">{{ $stats['favorites_total'] }}</p><p class="account-stat__label">{{ __('account.stats_favorites') }}</p></div>
        <div class="account-stat glass-luxury"><p class="account-stat__value">{{ $stats['rsvp_total'] }}</p><p class="account-stat__label">{{ __('account.stats_rsvp') }}</p></div>
    </div>

    <div class="account-card glass-luxury">
        <div class="flex items-center justify-between gap-4 mb-4">
            <h2 class="font-serif text-xl font-semibold text-ink">{{ __('account.recent_orders') }}</h2>
            <a href="{{ route('account.orders') }}" class="text-sm font-semibold text-luxury-gold-dark hover:underline">{{ __('account.view_all') }} →</a>
        </div>
        @forelse ($recentOrders as $order)
            <div class="account-order-row account-order-row--stacked">
                <div>
                    <p class="font-semibold text-ink">{{ $order->coupleTitle() }}</p>
                    <p class="text-sm text-ink-muted mt-0.5">{{ $order->event_type }} · {{ $order->formattedEventDate() }}</p>
                    @if ($order->isPublished() && $order->id !== ($liveInvitation->id ?? null))
                        <div class="mt-3">
                            <x-invitation.share-bar :invitation="$order" :compact="true" :show-platform-link="false" />
                        </div>
                    @endif
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <span class="account-badge {{ $order->isPublished() ? 'account-badge--published' : 'account-badge--draft' }}">{{ $order->statusLabel() }}</span>
                    <a href="{{ route('builder.edit', $order) }}" class="text-sm font-semibold text-ink hover:text-luxury-gold-dark">{{ __('account.edit') }}</a>
                </div>
            </div>
        @empty
            <div class="text-center py-10">
                <p class="text-ink-soft">{{ __('account.no_orders') }}</p>
                <a href="{{ route('builder.create') }}" class="btn-gold-shimmer btn-shine mt-4 inline-flex" data-ripple>{{ __('account.create_first') }}</a>
            </div>
        @endforelse
    </div>
@endsection
