<header class="nav-glass" id="site-nav" role="banner">
    <div class="nav-glass__inner mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- Logo --}}
        <a href="/" class="flex shrink-0 items-center gap-2.5 no-underline">
            <div
                class="flex h-10 w-10 items-center justify-center rounded-xl glass-luxury font-serif text-lg font-semibold text-luxury-gold-dark"
                aria-hidden="true"
            >
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

        {{-- Desktop nav --}}
        <nav class="hidden items-center gap-6 lg:flex lg:gap-8" aria-label="Asosiy navigatsiya">
            <a href="#shablonlar" class="nav-link">Shablonlar</a>
            <a href="#xizmatlar" class="nav-link">Xizmatlar</a>
            <a href="#narxlar" class="nav-link">Narxlar</a>
            <a href="#haqida" class="nav-link">Biz haqimizda</a>
        </nav>

        {{-- Actions --}}
        <div class="flex items-center gap-2 sm:gap-3">
            <button
                type="button"
                id="theme-toggle"
                class="theme-toggle-btn hidden rounded-full px-3 py-2 text-xs font-medium text-ink-soft glass-luxury sm:inline-flex sm:text-sm"
                aria-label="Mavzu rejimini almashtirish"
                aria-pressed="false"
            >
                Qorong'u rejim
            </button>

            <a href="#boshlash" class="btn-nav-cta hidden sm:inline-flex" data-ripple>
                Taklifnoma Yaratish
            </a>

            <button
                type="button"
                id="mobile-menu-toggle"
                class="flex h-10 w-10 items-center justify-center rounded-xl glass-luxury lg:hidden"
                aria-label="Menyuni ochish"
                aria-expanded="false"
                aria-controls="mobile-nav"
            >
                <svg class="h-5 w-5 text-ink dark:text-cream-100" id="menu-icon-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
                <svg class="hidden h-5 w-5 text-ink dark:text-cream-100" id="menu-icon-close" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/>
                </svg>
            </button>
        </div>
    </div>
</header>

{{-- Mobile drawer --}}
<div class="mobile-nav lg:hidden" id="mobile-nav" aria-hidden="true">
    <div class="mobile-nav__backdrop" data-close-mobile-nav></div>
    <nav class="mobile-nav__panel" aria-label="Mobil navigatsiya">
        <ul class="flex flex-col gap-1">
            <li><a href="#shablonlar" class="block rounded-lg px-4 py-3 text-base font-medium text-ink hover:bg-white/50" data-close-mobile-nav>Shablonlar</a></li>
            <li><a href="#xizmatlar" class="block rounded-lg px-4 py-3 text-base font-medium text-ink hover:bg-white/50" data-close-mobile-nav>Xizmatlar</a></li>
            <li><a href="#narxlar" class="block rounded-lg px-4 py-3 text-base font-medium text-ink hover:bg-white/50" data-close-mobile-nav>Narxlar</a></li>
            <li><a href="#haqida" class="block rounded-lg px-4 py-3 text-base font-medium text-ink hover:bg-white/50" data-close-mobile-nav>Biz haqimizda</a></li>
        </ul>
        <div class="mt-6 flex flex-col gap-3 border-t border-white/40 pt-6">
            <button type="button" id="theme-toggle-mobile" class="theme-toggle-btn rounded-full px-4 py-3 text-sm font-medium glass-luxury text-ink-soft">
                Qorong'u rejim
            </button>
            <a href="#boshlash" class="btn-gold-shimmer w-full text-center" data-ripple data-close-mobile-nav>
                Taklifnoma Yaratish
            </a>
        </div>
    </nav>
</div>
