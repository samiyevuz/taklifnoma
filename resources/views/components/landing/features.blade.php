<section id="xizmatlar" class="relative py-16 sm:py-20 lg:py-28" aria-labelledby="features-heading">
    <div class="landing-container">
        <div class="reveal mx-auto max-w-2xl text-center">
            <p class="section-label mb-4 justify-center">Nima Uchun Biz?</p>
            <h2 id="features-heading" class="font-serif text-display font-semibold text-ink text-balance">
                Boshqa Platformalardan Farq Qiladigan Imkoniyatlar
            </h2>
            <p class="mt-4 text-fluid-body text-ink-soft text-pretty">
                Faqat chiroyli dizayn emas — to'liq tadbir boshqaruv ekotizimi. Mehmonlar, musiqa,
                dress-code va statistika — barchasi bir joyda.
            </p>
        </div>

        <div class="mt-14 grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-12 lg:items-start">
            {{-- Feature cards --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ([
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                        'title' => 'Jonli RSVP Kuzatuv',
                        'desc' => 'Kim keladi, kim kelmaydi — real vaqtda yangilanadigan interaktiv panel.',
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>',
                        'title' => 'Fon Musiqasi Tanlash',
                        'desc' => 'Taklifnoma ochilganda premium fon musiqasi — kayfiyatni darhol yaratadi.',
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>',
                        'title' => 'Dress Code Palitrasi',
                        'desc' => 'Mehmonlar uchun aniq rang kodlari va vizual palitra namoyishi.',
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
                        'title' => 'Mehmonlar Hisoblagichi',
                        'desc' => 'Kattalar, bolalar, o\'rinlar — barchasi avtomatik hisoblanadi va eksport qilinadi.',
                    ],
                ] as $i => $feature)
                    <div class="feature-card reveal {{ $i > 0 ? 'reveal-delay-' . min($i, 3) : '' }}">
                        <div class="feature-icon">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                {!! $feature['icon'] !!}
                            </svg>
                        </div>
                        <h3 class="font-semibold text-ink">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-muted">{{ $feature['desc'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- RSVP Live Preview Panel --}}
            <div class="reveal reveal-delay-2">
                <div class="rsvp-panel" id="rsvp-preview">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold tracking-widest text-luxury-gold-dark uppercase">Jonli Panel</p>
                            <h3 class="mt-1 font-serif text-xl font-semibold text-ink">RSVP Statistikasi</h3>
                        </div>
                        <span class="flex items-center gap-1.5 rounded-full bg-luxury-emerald/15 px-3 py-1 text-xs font-semibold text-luxury-emerald">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-luxury-emerald opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-luxury-emerald"></span>
                            </span>
                            Live
                        </span>
                    </div>

                    <div class="mb-6 grid grid-cols-3 gap-3 text-center">
                        <div class="rsvp-stat-box rounded-xl p-3">
                            <p class="font-serif text-2xl font-bold" data-rsvp-count="accepted">142</p>
                            <p class="mt-0.5 text-xs">Keladi</p>
                        </div>
                        <div class="rsvp-stat-box rounded-xl p-3">
                            <p class="font-serif text-2xl font-bold" data-rsvp-count="declined">18</p>
                            <p class="mt-0.5 text-xs">Kelmaydi</p>
                        </div>
                        <div class="rsvp-stat-box rounded-xl p-3">
                            <p class="font-serif text-2xl font-bold" data-rsvp-count="pending">37</p>
                            <p class="mt-0.5 text-xs">Kutilmoqda</p>
                        </div>
                    </div>

                    <p class="mb-2 text-sm font-medium text-ink-soft">Javoblar dinamikasi</p>
                    <div class="rsvp-bar">
                        <div class="rsvp-bar__fill" id="rsvp-bar-fill" style="width: 72%"></div>
                    </div>
                    <p class="mt-2 text-right text-xs text-ink-muted"><span id="rsvp-percent">72</span>% tasdiqlangan</p>

                    <ul class="mt-6 space-y-0">
                        @foreach ([
                            ['name' => 'Dilnoza Karimova', 'status' => 'Keladi', 'color' => 'text-luxury-emerald'],
                            ['name' => 'Jasur Toshmatov', 'status' => 'Keladi', 'color' => 'text-luxury-emerald'],
                            ['name' => 'Malika Rahimova', 'status' => 'Kutilmoqda', 'color' => 'text-luxury-gold-dark'],
                            ['name' => 'Bobur Nazarov', 'status' => 'Kelmaydi', 'color' => 'text-ink-muted'],
                        ] as $guest)
                            <li class="rsvp-stat">
                                <span class="text-sm font-medium text-ink">{{ $guest['name'] }}</span>
                                <span class="text-xs font-semibold {{ $guest['color'] }}">{{ $guest['status'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
