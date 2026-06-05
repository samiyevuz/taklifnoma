@extends('layouts.account')

@section('account')
    <div class="mb-8">
        <p class="section-label mb-3">Kabinet</p>
        <h1 class="font-serif text-display font-semibold text-ink">Xush kelibsiz, {{ auth()->user()->name }}</h1>
        <p class="mt-2 text-ink-soft">Taklifnomalaringiz va yoqtirgan shablonlaringizni shu yerdan boshqaring.</p>
    </div>

    <div class="account-stat-grid mb-8">
        <div class="account-stat glass-luxury">
            <p class="account-stat__value">{{ $stats['orders_total'] }}</p>
            <p class="account-stat__label">Jami zakazlar</p>
        </div>
        <div class="account-stat glass-luxury">
            <p class="account-stat__value">{{ $stats['orders_published'] }}</p>
            <p class="account-stat__label">Nashr qilingan</p>
        </div>
        <div class="account-stat glass-luxury">
            <p class="account-stat__value">{{ $stats['favorites_total'] }}</p>
            <p class="account-stat__label">Yoqtirganlar</p>
        </div>
        <div class="account-stat glass-luxury">
            <p class="account-stat__value">{{ $stats['rsvp_total'] }}</p>
            <p class="account-stat__label">RSVP javoblar</p>
        </div>
    </div>

    <div class="account-card glass-luxury">
        <div class="flex items-center justify-between gap-4 mb-4">
            <h2 class="font-serif text-xl font-semibold text-ink">So'nggi zakazlar</h2>
            <a href="{{ route('account.orders') }}" class="text-sm font-semibold text-luxury-gold-dark hover:underline">Barchasi →</a>
        </div>

        @forelse ($recentOrders as $order)
            <div class="account-order-row">
                <div>
                    <p class="font-semibold text-ink">{{ $order->coupleTitle() }}</p>
                    <p class="text-sm text-ink-muted mt-0.5">{{ $order->event_type }} · {{ $order->formattedEventDate() }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="account-badge {{ $order->isPublished() ? 'account-badge--published' : 'account-badge--draft' }}">
                        {{ $order->statusLabel() }}
                    </span>
                    <a href="{{ route('builder.edit', $order) }}" class="text-sm font-semibold text-ink hover:text-luxury-gold-dark">Tahrirlash</a>
                </div>
            </div>
        @empty
            <div class="text-center py-10">
                <p class="text-ink-soft">Hali zakazlar yo'q.</p>
                <a href="{{ route('builder.create') }}" class="btn-gold-shimmer btn-shine mt-4 inline-flex" data-ripple>
                    Birinchi taklifnomani yaratish
                </a>
            </div>
        @endforelse
    </div>
@endsection
