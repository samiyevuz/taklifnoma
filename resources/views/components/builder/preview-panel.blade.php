<div class="builder-preview" id="builder-preview-panel" aria-label="{{ __('builder.live_preview') }}">
    <div class="builder-preview__label">
        <span class="builder-preview__dot" aria-hidden="true"></span>
        {{ __('builder.live_preview') }}
    </div>

    <div class="builder-variant-carousel" id="builder-variant-carousel">
        <button
            type="button"
            class="builder-variant-nav builder-variant-nav--prev"
            id="builder-variant-prev"
            aria-label="{{ __('builder.variant_prev') }}"
            hidden
        >
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div class="builder-variant-carousel__stage">
            <div class="builder-phone builder-phone--tier-2" id="builder-phone">
                <div class="builder-phone__frame">
                    <div class="builder-phone__island" aria-hidden="true"></div>
                    <div class="builder-phone__screen" id="builder-phone-screen">
                        <div class="builder-preview-viewport">
                            <div class="builder-preview-scaler">
                                <div class="invitation-page builder-preview-page inv-theme--premium inv-anim--enhanced" id="builder-preview">
                                    <div class="builder-preview-fx" id="builder-preview-fx" aria-hidden="true">
                                        <div class="builder-preview-cover" id="builder-preview-cover"></div>
                                        <div class="builder-preview-overlay"></div>
                                        <div class="builder-preview-shimmer"></div>
                                        <div class="builder-preview-particles" id="builder-preview-particles"></div>
                                    </div>
                                    <span class="builder-preview-tier-ribbon hidden" id="builder-preview-tier-ribbon"></span>

                                    <main class="invitation-content">
                                        <section class="inv-welcome" aria-hidden="false">
                                            <div class="inv-welcome__ornament" aria-hidden="true">
                                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" d="M12 3c-1.5 2-4 4-4 7a4 4 0 108 0c0-3-2.5-5-4-7z"/>
                                                    <path stroke-linecap="round" d="M12 14v4M9 20h6"/>
                                                </svg>
                                            </div>
                                            <p class="inv-welcome__pre" data-preview="event_type"></p>

                                            <div class="inv-welcome__hero" data-preview-layout="couple couple_bride_first general">
                                                <h1 class="inv-welcome__names inv-welcome__names--dual">
                                                    <span data-preview="hero_primary"></span>
                                                    <span class="inv-welcome__amp" data-preview="hero_connector">&</span>
                                                    <span data-preview="hero_secondary"></span>
                                                </h1>
                                            </div>

                                            <div class="inv-welcome__hero hidden" data-preview-layout="child celebrant">
                                                <h1 class="inv-welcome__names inv-welcome__names--single">
                                                    <span data-preview="hero_primary_single"></span>
                                                </h1>
                                                <p class="inv-welcome__tagline" data-preview="hero_tagline"></p>
                                                <p class="inv-welcome__hosts hidden" data-preview="hero_hosts_wrap">
                                                    <span data-preview="hero_hosts"></span>
                                                </p>
                                            </div>

                                            <div class="inv-welcome__hero hidden" data-preview-layout="graduation">
                                                <h1 class="inv-welcome__names inv-welcome__names--stacked">
                                                    <span data-preview="hero_primary_stacked"></span>
                                                    <span class="inv-welcome__stacked-sub" data-preview="hero_secondary_stacked"></span>
                                                </h1>
                                                <p class="inv-welcome__tagline" data-preview="hero_tagline_graduation"></p>
                                            </div>

                                            <p class="inv-welcome__date" data-preview="welcome_subtitle"></p>
                                            <button type="button" class="inv-welcome__scroll" tabindex="-1" aria-hidden="true">
                                                <span>{{ __('invitation.open_invitation') }}</span>
                                            </button>
                                        </section>

                                        <section class="inv-section" aria-hidden="true">
                                            <div class="inv-reveal">
                                                <p class="inv-section-label">{{ __('invitation.section_invitation') }}</p>
                                                <h2 class="inv-section-title">{!! __('invitation.section_invitation_title') !!}</h2>
                                                <div class="inv-text-card inv-glass">
                                                    <p data-preview="invitation_text_1"></p>
                                                    <p data-preview="invitation_text_2" class="hidden" data-preview-optional></p>
                                                    <p class="mt-4 font-serif text-inv-ink hidden" data-preview="family_signature_wrap">
                                                        {{ __('invitation.family_from') }} <em data-preview="family_signature"></em>
                                                    </p>
                                                    <div class="inv-countdown" role="presentation">
                                                        <div class="inv-countdown__item"><span class="inv-countdown__value" data-preview="cd-days">00</span><span class="inv-countdown__label">{{ __('invitation.countdown_days') }}</span></div>
                                                        <div class="inv-countdown__item"><span class="inv-countdown__value" data-preview="cd-hours">00</span><span class="inv-countdown__label">{{ __('invitation.countdown_hours') }}</span></div>
                                                        <div class="inv-countdown__item"><span class="inv-countdown__value" data-preview="cd-minutes">00</span><span class="inv-countdown__label">{{ __('invitation.countdown_minutes') }}</span></div>
                                                        <div class="inv-countdown__item"><span class="inv-countdown__value" data-preview="cd-seconds">00</span><span class="inv-countdown__label">{{ __('invitation.countdown_seconds') }}</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                        <section class="inv-section" aria-hidden="true">
                                            <div class="inv-reveal">
                                                <p class="inv-section-label">{{ __('invitation.dress_code') }}</p>
                                                <h2 class="inv-section-title">{{ __('invitation.dress_code_title') }}</h2>
                                                <div class="inv-dress-grid builder-preview-interactive" id="builder-preview-dress" role="list"></div>
                                                <p class="inv-dress-note" id="builder-preview-dress-note" aria-live="polite"></p>
                                            </div>
                                        </section>

                                        <section class="inv-section builder-preview-rsvp" id="builder-preview-rsvp" aria-hidden="true">
                                            <div class="inv-reveal">
                                                <p class="inv-section-label">RSVP</p>
                                                <h2 class="inv-section-title">{{ __('invitation.rsvp_title') }}</h2>
                                                <div class="inv-rsvp-card inv-glass">
                                                    <p class="text-center text-sm text-inv-ink-soft">{{ __('invitation.rsvp_desc') }}</p>
                                                </div>
                                            </div>
                                        </section>

                                        <section class="inv-section" aria-hidden="true">
                                            <div class="inv-reveal">
                                                <p class="inv-section-label">{{ __('invitation.location') }}</p>
                                                <h2 class="inv-section-title">{{ __('invitation.location_title') }}</h2>
                                                <div class="inv-location-card inv-glass">
                                                    <p class="font-serif text-lg font-semibold text-inv-ink" data-preview="venue_name"></p>
                                                    <p class="mt-2 text-sm text-inv-ink-soft" data-preview="venue_address"></p>
                                                    <div id="builder-preview-map" class="builder-preview-map builder-preview-interactive hidden" aria-hidden="true"></div>
                                                </div>
                                            </div>
                                        </section>
                                    </main>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button
            type="button"
            class="builder-variant-nav builder-variant-nav--next"
            id="builder-variant-next"
            aria-label="{{ __('builder.variant_next') }}"
            hidden
        >
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    <div class="builder-variant-meta" id="builder-variant-meta">
        <div class="builder-variant-meta__dots" id="builder-variant-dots" role="tablist" aria-label="{{ __('builder.variant_choose') }}"></div>
        <div class="builder-variant-meta__head">
            <p class="builder-variant-meta__title" id="builder-variant-title"></p>
            <span class="builder-variant-meta__badge hidden" id="builder-variant-badge"></span>
        </div>
        <p class="builder-variant-meta__subtitle" id="builder-variant-subtitle"></p>
        <p class="builder-variant-meta__price" id="builder-variant-price"></p>
        <div class="builder-variant-meta__features" id="builder-variant-features" aria-label="{{ __('builder.variant_features') }}"></div>
    </div>
</div>
