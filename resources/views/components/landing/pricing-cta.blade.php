<section id="narxlar" class="relative py-16 sm:py-20" aria-labelledby="pricing-heading">
    <div class="landing-container">
        <div class="reveal pricing-glass glass-luxury rounded-3xl p-6 sm:p-12 lg:p-16 text-center">
            <div class="pricing-sticky-header">
                <p class="section-label mb-4 justify-center">{{ __('landing.pricing_label') }}</p>
                <h2 id="pricing-heading" class="font-serif text-display font-semibold text-ink text-balance">{{ __('landing.pricing_title') }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-fluid-body text-ink-soft">{{ __('landing.pricing_desc') }}</p>
            </div>

            <div class="pricing-scroll-hint mt-6 sm:hidden" aria-hidden="true">
                <span class="text-xs text-ink-muted">{{ __('landing.pricing_scroll') }}</span>
            </div>

            <div class="pricing-scroll-wrapper mt-6 sm:mt-10">
                <div class="pricing-scroll-track sm:grid sm:grid-cols-3 sm:gap-6">
                    <div class="pricing-tier rounded-2xl p-5 sm:p-6">
                        <p class="pricing-tier__label text-sm font-semibold text-ink-muted uppercase tracking-wider">{{ __('landing.pricing_tier_starter') }}</p>
                        <p class="mt-2 font-serif text-2xl font-bold text-ink sm:text-3xl">{{ __('landing.pricing_free') }}</p>
                        <p class="mt-2 text-sm text-ink-muted">{{ __('landing.pricing_starter_desc') }}</p>
                    </div>
                    <div class="pricing-tier pricing-tier--featured relative rounded-2xl p-5 sm:p-6">
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-luxury-gold px-3 py-0.5 text-xs font-bold text-royal-950">{{ __('landing.pricing_recommended') }}</span>
                        <p class="pricing-tier__label text-sm font-semibold text-luxury-gold-dark uppercase tracking-wider">{{ __('landing.pricing_tier_premium') }}</p>
                        <p class="mt-2 font-serif text-2xl font-bold text-ink sm:text-3xl">149 000 <span class="text-base font-normal text-ink-muted">{{ __('landing.currency') }}</span></p>
                        <p class="mt-2 text-sm text-ink-muted">{{ __('landing.pricing_premium_desc') }}</p>
                    </div>
                    <div class="pricing-tier rounded-2xl p-5 sm:p-6">
                        <p class="pricing-tier__label text-sm font-semibold text-ink-muted uppercase tracking-wider">{{ __('landing.pricing_tier_vip') }}</p>
                        <p class="mt-2 font-serif text-2xl font-bold text-ink sm:text-3xl">299 000 <span class="text-base font-normal text-ink-muted">{{ __('landing.currency') }}</span></p>
                        <p class="mt-2 text-sm text-ink-muted">{{ __('landing.pricing_vip_desc') }}</p>
                    </div>
                </div>
            </div>

            <a href="{{ auth()->check() ? route('builder.create') : route('login') }}" class="btn-gold-shimmer btn-shine mt-8 inline-flex sm:mt-10" data-ripple>
                {{ __('landing.pricing_cta') }}
            </a>
        </div>
    </div>
</section>
