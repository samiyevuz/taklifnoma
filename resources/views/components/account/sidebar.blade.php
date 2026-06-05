@php $current = request()->route()->getName(); @endphp

<aside class="account-sidebar glass-luxury rounded-2xl p-4">
    <div class="mb-4"><x-ui.locale-switcher /></div>

    <div class="account-user-card">
        <div class="account-avatar" aria-hidden="true">{{ auth()->user()->initials() }}</div>
        <div class="min-w-0">
            <p class="font-semibold text-ink truncate">{{ auth()->user()->name }}</p>
            <p class="text-xs text-ink-muted truncate">{{ auth()->user()->email }}</p>
        </div>
    </div>

    <nav class="account-nav" aria-label="{{ __('nav.cabinet') }}">
        <a href="{{ route('account.dashboard') }}" class="account-nav__link {{ $current === 'account.dashboard' ? 'is-active' : '' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h8M4 18h16"/></svg>
            {{ __('account.dashboard') }}
        </a>
        <a href="{{ route('account.orders') }}" class="account-nav__link {{ $current === 'account.orders' ? 'is-active' : '' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            {{ __('account.orders') }}
        </a>
        <a href="{{ route('account.favorites') }}" class="account-nav__link {{ $current === 'account.favorites' ? 'is-active' : '' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            {{ __('account.favorites') }}
        </a>
        <a href="{{ route('account.profile') }}" class="account-nav__link {{ str_starts_with($current, 'account.profile') ? 'is-active' : '' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            {{ __('account.profile') }}
        </a>
    </nav>

    <div class="mt-6 pt-4 border-t border-white/40 space-y-3">
        <a href="{{ url('/') }}" class="account-nav__link account-nav__link--site">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" d="M3 12l9-9 9 9M5 10v10h14V10"/>
            </svg>
            {{ __('account.back_to_site') }}
        </a>
        <a href="{{ route('builder.create') }}" class="btn-gold-shimmer btn-shine w-full text-center text-sm" data-ripple>{{ __('account.new_invitation') }}</a>
        <form action="{{ route('logout') }}" method="POST" class="mt-3">
            @csrf
            <button type="submit" class="btn-outline-luxury w-full text-sm">{{ __('nav.logout') }}</button>
        </form>
    </div>
</aside>
