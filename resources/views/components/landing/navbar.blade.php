@php
    $createUrl = auth()->check() ? route('builder.create') : '#shablonlar';
@endphp

<header class="nav-glass" id="site-nav" role="banner">
    <div class="nav-glass__inner mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <a href="/" class="flex shrink-0 items-center gap-2.5 no-underline">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl glass-luxury font-serif text-lg font-semibold text-luxury-gold-dark" aria-hidden="true">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M12 2L4 8v14h16V8L12 2z" stroke-linejoin="round"/>
                    <path d="M12 8v8M9 11h6" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="hidden min-[360px]:block">
                <span class="font-serif text-base font-semibold tracking-tight text-ink dark:text-cream-50 sm:text-lg">Taklifnoma</span>
                <span class="block text-[0.65rem] font-medium tracking-widest text-luxury-gold-dark uppercase">Premium</span>
            </div>
        </a>

        <nav class="hidden items-center gap-6 lg:flex lg:gap-8" aria-label="{{ __('nav.main_nav') }}">
            <a href="#shablonlar" class="nav-link">{{ __('nav.templates') }}</a>
            <a href="#xizmatlar" class="nav-link">{{ __('nav.services') }}</a>
            <a href="#narxlar" class="nav-link">{{ __('nav.pricing') }}</a>
            <a href="#haqida" class="nav-link">{{ __('nav.about') }}</a>
        </nav>

        <div class="nav-actions">
            <div class="hidden lg:block">
                <x-ui.locale-switcher />
            </div>

            <button
                type="button"
                id="theme-toggle"
                class="nav-icon-btn theme-toggle-btn hidden sm:inline-flex"
                aria-label="{{ __('nav.toggle_theme') }}"
                aria-pressed="false"
                data-theme-icon-only="true"
                data-theme-label-dark="{{ __('nav.theme_dark') }}"
                data-theme-label-light="{{ __('nav.theme_light') }}"
            >
                <svg class="nav-icon-btn__icon theme-icon-moon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
                <svg class="nav-icon-btn__icon theme-icon-sun hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" d="M12 3v2m0 14v2M5.6 5.6l1.4 1.4m10 10l1.4 1.4M3 12h2m14 0h2M5.6 18.4l1.4-1.4m10-10l1.4-1.4M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                </svg>
            </button>

            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('account.dashboard') }}" class="nav-user-pill hidden md:inline-flex">
                    <span class="account-avatar !w-7 !h-7 !text-xs">{{ auth()->user()->initials() }}</span>
                    <span class="text-sm font-medium text-ink max-w-[7rem] truncate">{{ auth()->user()->name }}</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="nav-login-link hidden sm:inline-flex">{{ __('nav.login') }}</a>
            @endauth

            <a href="{{ $createUrl }}" class="btn-nav-cta btn-shine hidden md:inline-flex" data-ripple>
                {{ __('nav.create_invitation') }}
            </a>

            <button type="button" id="mobile-menu-toggle" class="nav-icon-btn lg:hidden" aria-label="{{ __('nav.open_menu') }}" aria-expanded="false" aria-controls="mobile-nav">
                <svg class="h-5 w-5 text-ink dark:text-cream-100" id="menu-icon-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                <svg class="hidden h-5 w-5 text-ink dark:text-cream-100" id="menu-icon-close" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>
    </div>
</header>

<div class="mobile-nav lg:hidden" id="mobile-nav" aria-hidden="true">
    <div class="mobile-nav__backdrop" data-close-mobile-nav aria-hidden="true"></div>
    <nav class="mobile-nav__panel" aria-label="{{ __('nav.mobile_nav') }}">
        <div class="mobile-nav__header">
            <div>
                <p class="mobile-nav__eyebrow">Taklifnoma</p>
                <p class="mobile-nav__title">{{ __('nav.menu') }}</p>
            </div>
            <button type="button" class="nav-icon-btn mobile-nav__close" data-close-mobile-nav aria-label="{{ __('nav.close_menu') }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/>
                </svg>
            </button>
        </div>

        <ul class="mobile-nav__links">
            <li><a href="#shablonlar" class="mobile-nav__link" data-close-mobile-nav>{{ __('nav.templates') }}</a></li>
            <li><a href="#xizmatlar" class="mobile-nav__link" data-close-mobile-nav>{{ __('nav.services') }}</a></li>
            <li><a href="#narxlar" class="mobile-nav__link" data-close-mobile-nav>{{ __('nav.pricing') }}</a></li>
            <li><a href="#haqida" class="mobile-nav__link" data-close-mobile-nav>{{ __('nav.about') }}</a></li>
            @auth
                @if(auth()->user()->isAdmin())
                    <li><a href="{{ route('admin.dashboard') }}" class="mobile-nav__link" data-close-mobile-nav>{{ __('admin.nav.dashboard') }}</a></li>
                @else
                    <li><a href="{{ route('account.dashboard') }}" class="mobile-nav__link" data-close-mobile-nav>{{ __('nav.cabinet') }}</a></li>
                @endif
            @endauth
        </ul>

        <div class="mobile-nav__footer">
            <div class="mobile-nav__tools">
                <x-ui.locale-switcher />
                <button
                    type="button"
                    id="theme-toggle-mobile"
                    class="nav-icon-btn theme-toggle-btn"
                    aria-label="{{ __('nav.toggle_theme') }}"
                    aria-pressed="false"
                    data-theme-icon-only="true"
                    data-theme-label-dark="{{ __('nav.theme_dark') }}"
                    data-theme-label-light="{{ __('nav.theme_light') }}"
                >
                    <svg class="nav-icon-btn__icon theme-icon-moon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                    <svg class="nav-icon-btn__icon theme-icon-sun hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M12 3v2m0 14v2M5.6 5.6l1.4 1.4m10 10l1.4 1.4M3 12h2m14 0h2M5.6 18.4l1.4-1.4m10-10l1.4-1.4M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                    </svg>
                </button>
            </div>

            <a href="{{ $createUrl }}" class="btn-gold-shimmer btn-shine w-full text-center" data-ripple data-close-mobile-nav>
                {{ __('nav.create_invitation') }}
            </a>

            @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-outline-luxury w-full">{{ __('nav.logout') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-outline-luxury w-full text-center" data-close-mobile-nav>{{ __('nav.login') }}</a>
            @endauth
        </div>
    </nav>
</div>
