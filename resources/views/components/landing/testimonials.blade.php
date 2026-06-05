@php
    $testimonials = [
        [
            'name' => 'Dilnoza Karimova',
            'role' => 'Nikoh to\'yi · Toshkent',
            'initials' => 'DK',
            'gradient' => 'from-luxury-purple to-luxury-purple-light',
            'quote' => 'Mehmonlarimiz taklifnomani ochganda hayratda qoldi. RSVP paneli tufayli 200 mehmonni boshqarish juda oson bo\'ldi. Dizayn darajasi haqiqatan premium.',
            'rating' => 5,
        ],
        [
            'name' => 'Jasur Toshmatov',
            'role' => 'Sunnat to\'yi · Samarqand',
            'initials' => 'JT',
            'gradient' => 'from-luxury-emerald to-luxury-emerald-light',
            'quote' => 'Fon musiqasi va dress-code funksiyalari ajoyib. Raqamli taklifnoma chop etishdan ko\'ra ancha zamonaviy va tejamkor yechim bo\'ldi.',
            'rating' => 5,
        ],
        [
            'name' => 'Malika Rahimova',
            'role' => 'Qiz uzatish · Buxoro',
            'initials' => 'MR',
            'gradient' => 'from-rose-400 to-luxury-gold-light',
            'quote' => '3 daqiqada tayyor taklifnoma yaratdim — shablonlar juda chiroyli. Mobil telefonda ham mukammal ko\'rinadi, hech qanday muammo bo\'lmadi.',
            'rating' => 5,
        ],
        [
            'name' => 'Bobur Nazarov',
            'role' => 'Korporativ tadbir · Andijon',
            'initials' => 'BN',
            'gradient' => 'from-royal-700 to-luxury-gold',
            'quote' => 'VIP paket bilan maxsus domen olish imkoniyati biznesimiz uchun professional ko\'rinish berdi. Qo\'llab-quvvatlash jamoasi tez javob beradi.',
            'rating' => 5,
        ],
    ];
@endphp

<section id="fikrlar" class="relative py-16 sm:py-20 lg:py-24" aria-labelledby="testimonials-heading">
    <div class="landing-container">
        <div class="reveal mx-auto max-w-2xl text-center">
            <p class="section-label mb-4 justify-center">Ishonch</p>
            <h2 id="testimonials-heading" class="font-serif text-display font-semibold text-ink text-balance">
                Mijozlarimiz Fikri
            </h2>
            <p class="mt-4 text-fluid-body text-ink-soft text-pretty">
                Minglab oilalar va tadbir tashkilotchilari Taklifnoma orqali unutilmas taassurot yaratmoqda.
            </p>
        </div>

        <div class="reveal reveal-delay-1 mt-12">
            <div class="testimonials-slider" id="testimonials-slider">
                <div class="testimonials-track" id="testimonials-track" role="region" aria-live="polite" aria-atomic="true">
                    @foreach ($testimonials as $index => $item)
                        <article
                            class="testimonial-card glass-luxury {{ $index === 0 ? 'is-active' : '' }}"
                            data-slide="{{ $index }}"
                            aria-hidden="{{ $index === 0 ? 'false' : 'true' }}"
                        >
                            <div class="flex items-start gap-4">
                                <div
                                    class="testimonial-avatar shrink-0 bg-gradient-to-br {{ $item['gradient'] }}"
                                    aria-hidden="true"
                                >
                                    <span>{{ $item['initials'] }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-serif text-lg font-semibold text-ink">{{ $item['name'] }}</h3>
                                        <div class="flex gap-0.5 text-luxury-gold" aria-label="{{ $item['rating'] }} yulduz">
                                            @for ($s = 0; $s < $item['rating']; $s++)
                                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="mt-0.5 text-sm text-ink-muted">{{ $item['role'] }}</p>
                                </div>
                            </div>
                            <blockquote class="mt-5">
                                <p class="font-serif text-lg leading-relaxed text-ink text-pretty sm:text-xl">
                                    &ldquo;{{ $item['quote'] }}&rdquo;
                                </p>
                            </blockquote>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8 flex items-center justify-center gap-4">
                    <button
                        type="button"
                        class="testimonial-nav"
                        id="testimonial-prev"
                        aria-label="Oldingi fikr"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    <div class="flex gap-2" role="tablist" aria-label="Fikrlar navigatsiyasi">
                        @foreach ($testimonials as $index => $item)
                            <button
                                type="button"
                                class="testimonial-dot {{ $index === 0 ? 'is-active' : '' }}"
                                data-slide-to="{{ $index }}"
                                role="tab"
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                aria-label="{{ $item['name'] }} fikri"
                            ></button>
                        @endforeach
                    </div>

                    <button
                        type="button"
                        class="testimonial-nav"
                        id="testimonial-next"
                        aria-label="Keyingi fikr"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
