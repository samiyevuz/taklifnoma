@php $current = request()->route()->getName(); @endphp

<aside class="admin-sidebar glass-luxury rounded-2xl p-4">
    <div class="mb-4 flex items-center justify-between gap-2">
        <p class="text-xs font-bold tracking-widest text-luxury-gold-dark uppercase">{{ __('admin.panel') }}</p>
        <x-ui.locale-switcher />
    </div>

    <div class="account-user-card">
        <div class="account-avatar admin-avatar" aria-hidden="true">AD</div>
        <div class="min-w-0">
            <p class="font-semibold text-ink truncate">{{ auth()->user()->name }}</p>
            <p class="text-xs text-luxury-emerald font-semibold">{{ __('admin.panel') }}</p>
        </div>
    </div>

    <nav class="account-nav" aria-label="{{ __('admin.panel') }}">
        <a href="{{ route('admin.dashboard') }}" class="account-nav__link {{ str_starts_with($current, 'admin.dashboard') || $current === 'admin.stats' ? 'is-active' : '' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h8M4 18h16"/></svg>
            {{ __('admin.dashboard') }}
        </a>
        <a href="{{ route('admin.users.index') }}" class="account-nav__link {{ str_starts_with($current, 'admin.users') ? 'is-active' : '' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
            {{ __('admin.users') }}
        </a>
        <a href="{{ route('admin.invitations.index') }}" class="account-nav__link {{ str_starts_with($current, 'admin.invitations') ? 'is-active' : '' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            {{ __('admin.invitations') }}
        </a>
        <a href="{{ route('admin.rsvps.index') }}" class="account-nav__link {{ str_starts_with($current, 'admin.rsvps') ? 'is-active' : '' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ __('admin.rsvps') }}
        </a>
        <a href="{{ route('admin.payments.index') }}" class="account-nav__link {{ str_starts_with($current, 'admin.payments') ? 'is-active' : '' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ __('admin.payments') }}
        </a>
    </nav>

    <div class="mt-6 pt-4 border-t border-white/40 space-y-3">
        <a href="{{ url('/') }}" class="account-nav__link account-nav__link--site">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>
            {{ __('admin.back_to_site') }}
        </a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-outline-luxury w-full text-sm">{{ __('nav.logout') }}</button>
        </form>
    </div>
</aside>
