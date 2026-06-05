<div class="builder-preview" id="builder-preview-panel" aria-label="{{ __('builder.live_preview') }}">
    <div class="builder-preview__label">
        <span class="builder-preview__dot" aria-hidden="true"></span>
        {{ __('builder.live_preview') }}
    </div>

    <div class="builder-phone" id="builder-phone">
        <div class="builder-phone__frame">
            <div class="builder-phone__island" aria-hidden="true"></div>
            <div class="builder-phone__screen" id="builder-phone-screen">
                <div class="builder-preview-viewport">
                    <div class="builder-preview-scaler">
                        <div class="invitation-page builder-preview-page" id="builder-preview">
                        <main class="invitation-content">
                            <section class="inv-welcome" aria-hidden="false">
                                <div class="inv-welcome__ornament" aria-hidden="true">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" d="M12 3c-1.5 2-4 4-4 7a4 4 0 108 0c0-3-2.5-5-4-7z"/>
                                        <path stroke-linecap="round" d="M12 14v4M9 20h6"/>
                                    </svg>
                                </div>
                                <p class="inv-welcome__pre" data-preview="event_type"></p>
                                <h1 class="inv-welcome__names">
                                    <span data-preview="groom_name"></span>
                                    <span class="inv-welcome__amp">&</span>
                                    <span data-preview="bride_name"></span>
                                </h1>
                                <p class="inv-welcome__date" data-preview="welcome_subtitle"></p>
                                <button type="button" class="inv-welcome__scroll" tabindex="-1" aria-hidden="true">
                                    <span>{{ __('invitation.open_invitation') }}</span>
                                </button>
                            </section>

                            <section class="inv-section" aria-hidden="true">
                                <div class="inv-reveal is-visible">
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
                                <div class="inv-reveal is-visible">
                                    <p class="inv-section-label">{{ __('invitation.dress_code') }}</p>
                                    <h2 class="inv-section-title">{{ __('invitation.dress_code_title') }}</h2>
                                    <div class="inv-dress-grid" id="builder-preview-dress" role="list"></div>
                                </div>
                            </section>

                            <section class="inv-section builder-preview-rsvp" id="builder-preview-rsvp" aria-hidden="true">
                                <div class="inv-reveal is-visible">
                                    <p class="inv-section-label">RSVP</p>
                                    <h2 class="inv-section-title">{{ __('invitation.rsvp_title') }}</h2>
                                    <div class="inv-rsvp-card inv-glass">
                                        <p class="text-center text-sm text-inv-ink-soft">{{ __('invitation.rsvp_desc') }}</p>
                                    </div>
                                </div>
                            </section>

                            <section class="inv-section" aria-hidden="true">
                                <div class="inv-reveal is-visible">
                                    <p class="inv-section-label">{{ __('invitation.location') }}</p>
                                    <h2 class="inv-section-title">{{ __('invitation.location_title') }}</h2>
                                    <div class="inv-location-card inv-glass">
                                        <p class="font-serif text-lg font-semibold text-inv-ink" data-preview="venue_name"></p>
                                        <p class="mt-2 text-sm text-inv-ink-soft" data-preview="venue_address"></p>
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
