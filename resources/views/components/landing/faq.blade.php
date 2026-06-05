@php
    $faqs = [
        [
            'q' => 'Taklifnoma yaratish qancha vaqt oladi?',
            'a' => 'Shablon tanlash, matn va rasmlarni tahrirlash — o\'rtacha 3–5 daqiqa. Tayyor bo\'lgach, havolani Telegram, WhatsApp yoki SMS orqali darhol ulashishingiz mumkin.',
        ],
        [
            'q' => 'Mehmonlar RSVP javobini qanday beradi?',
            'a' => 'Har bir taklifnoma ichida "Kelaman" / "Kelolmayman" tugmalari mavjud. Javoblar jonli panelingizda real vaqtda yangilanadi va Excel formatida eksport qilish mumkin.',
        ],
        [
            'q' => 'Mobil telefonda ham yaxshi ko\'rinadimi?',
            'a' => 'Ha. Barcha shablonlar 320px ekrandan boshlab to\'liq optimallashtirilgan. iPhone SE, Android budget qurilmalar va planshetlarda mukammal ishlaydi.',
        ],
        [
            'q' => 'Fon musiqasini o\'zgartirish mumkinmi?',
            'a' => 'Premium va VIP paketlarda 20+ litsenziyalangan fon musiqalari kutubxonasidan tanlash yoki o\'z audio faylingizni yuklash imkoniyati mavjud.',
        ],
        [
            'q' => 'To\'lov qanday amalga oshiriladi?',
            'a' => 'Payme, Click va bank kartalari orqali xavfsiz to\'lov. Bepul rejimda 30 mehmon va 1 shablon bilan platformani sinab ko\'rishingiz mumkin.',
        ],
        [
            'q' => 'Taklifnomani tahrirlash yoki bekor qilish mumkinmi?',
            'a' => 'Albatta. Yaratilgan taklifnomani istalgan vaqtda tahrirlashingiz, mehmonlar ro\'yxatini yangilashingiz va havolani o\'chirishingiz mumkin.',
        ],
    ];
@endphp

<section id="savollar" class="relative py-16 sm:py-20 lg:py-24" aria-labelledby="faq-heading">
    <div class="landing-container">
        <div class="reveal mx-auto max-w-2xl text-center">
            <p class="section-label mb-4 justify-center">Yordam</p>
            <h2 id="faq-heading" class="font-serif text-display font-semibold text-ink text-balance">
                Savol-Javoblar
            </h2>
            <p class="mt-4 text-fluid-body text-ink-soft">
                Eng ko\'p beriladigan savollarga tez javoblar.
            </p>
        </div>

        <div class="reveal reveal-delay-1 mx-auto mt-12 max-w-3xl">
            <div class="faq-accordion" id="faq-accordion">
                @foreach ($faqs as $index => $faq)
                    <div class="faq-item glass-luxury {{ $index === 0 ? 'is-open' : '' }}">
                        <button
                            type="button"
                            class="faq-trigger"
                            id="faq-trigger-{{ $index }}"
                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                            aria-controls="faq-panel-{{ $index }}"
                        >
                            <span class="font-medium text-ink text-left">{{ $faq['q'] }}</span>
                            <span class="faq-icon" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" d="M12 6v12M6 12h12"/>
                                </svg>
                            </span>
                        </button>
                        <div
                            class="faq-panel"
                            id="faq-panel-{{ $index }}"
                            role="region"
                            aria-labelledby="faq-trigger-{{ $index }}"
                            @if ($index !== 0) aria-hidden="true" @endif
                        >
                            <p class="text-ink-soft leading-relaxed">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
