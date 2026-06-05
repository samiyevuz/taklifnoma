@php $faqs = __('landing.faqs'); @endphp

<section id="savollar" class="relative py-16 sm:py-20 lg:py-24" aria-labelledby="faq-heading">
    <div class="landing-container">
        <div class="reveal mx-auto max-w-2xl text-center">
            <p class="section-label mb-4 justify-center">{{ __('landing.faq_label') }}</p>
            <h2 id="faq-heading" class="font-serif text-display font-semibold text-ink text-balance">{{ __('landing.faq_title') }}</h2>
            <p class="mt-4 text-fluid-body text-ink-soft">{{ __('landing.faq_desc') }}</p>
        </div>

        <div class="mx-auto mt-12 max-w-3xl space-y-3">
            @foreach ($faqs as $index => $faq)
                <details class="faq-item reveal {{ $index > 0 ? 'reveal-delay-' . min($index, 4) : '' }}" {{ $index === 0 ? 'open' : '' }}>
                    <summary class="faq-item__question">
                        <span>{{ $faq['q'] }}</span>
                        <svg class="faq-item__icon h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="faq-item__answer">
                        <p>{{ $faq['a'] }}</p>
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>
