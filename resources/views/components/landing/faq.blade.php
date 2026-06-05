@php
    use App\Support\SiteContent;

    $faqMeta = SiteContent::faqMeta();
    $faqs = SiteContent::faqs();
@endphp

<section id="savollar" class="relative py-16 sm:py-20 lg:py-24" aria-labelledby="faq-heading">
    <div class="landing-container">
        <div class="reveal mx-auto max-w-2xl text-center">
            <p class="section-label mb-4 justify-center">{{ $faqMeta['label'] }}</p>
            <h2 id="faq-heading" class="font-serif text-display font-semibold text-ink text-balance">{{ $faqMeta['title'] }}</h2>
            <p class="mt-4 text-fluid-body text-ink-soft">{{ $faqMeta['desc'] }}</p>
        </div>

        <div class="faq-accordion mx-auto mt-12 max-w-3xl" id="faq-accordion">
            @foreach ($faqs as $index => $faq)
                <div class="faq-item glass-luxury reveal {{ $index === 0 ? 'is-open' : '' }} {{ $index > 0 ? 'reveal-delay-' . min($index, 4) : '' }}">
                    <button
                        type="button"
                        class="faq-trigger"
                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                    >
                        <span class="font-medium text-ink">{{ $faq['q'] }}</span>
                        <span class="faq-icon" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" d="M12 5v14M5 12h14"/>
                            </svg>
                        </span>
                    </button>
                    <div class="faq-panel" aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
                        <p class="text-sm leading-relaxed text-ink-soft">{{ $faq['a'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
