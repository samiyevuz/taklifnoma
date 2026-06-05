@extends('layouts.account')

@section('account')
    <div class="mb-8">
        <p class="section-label mb-3">{{ __('account.orders') }}</p>
        <h1 class="font-serif text-display font-semibold text-ink">{{ __('account.orders_title') }}</h1>
        <p class="mt-2 text-ink-soft">{{ __('account.orders_subtitle') }}</p>
    </div>

    <div class="account-card glass-luxury">
        @forelse ($orders as $order)
            <div class="account-order-row">
                <div class="min-w-0">
                    <p class="font-semibold text-ink">{{ $order->coupleTitle() }}</p>
                    <p class="text-sm text-ink-muted mt-0.5">{{ $order->event_type }} · {{ $order->formattedEventDate() }} · {{ $order->rsvp_responses_count }} RSVP</p>
                    @if ($order->isPublished())
                        <a href="{{ route('invitation.show', $order->slug) }}" target="_blank" class="text-xs text-luxury-gold-dark mt-1 inline-block hover:underline">/i/{{ $order->slug }}</a>
                    @endif
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="account-badge {{ $order->isPublished() ? 'account-badge--published' : 'account-badge--draft' }}">{{ $order->statusLabel() }}</span>
                    <a href="{{ route('builder.edit', $order) }}" class="text-sm font-semibold text-ink hover:text-luxury-gold-dark">{{ __('account.edit') }}</a>
                    @if ($order->isPublished())
                        <a href="{{ route('invitation.show', $order->slug) }}" target="_blank" class="text-sm font-semibold text-luxury-gold-dark hover:underline">{{ __('account.view') }}</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <p class="text-ink-soft">{{ __('account.orders_empty') }}</p>
                <a href="{{ route('builder.create') }}" class="btn-gold-shimmer btn-shine mt-4 inline-flex" data-ripple>{{ __('account.create_invitation') }}</a>
            </div>
        @endforelse
        @if ($orders->hasPages())
            <div class="mt-6 pt-4 border-t border-white/30">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
