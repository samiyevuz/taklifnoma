@extends('layouts.invitation')

@section('body')
    <div
        class="invitation-page"
        id="invitation-app"
        data-invitation-slug="{{ $invitation->slug }}"
    >
        <button
            type="button"
            class="inv-music"
            id="inv-music"
            aria-label="Fon musiqasini yoqish"
            aria-pressed="false"
        >
            <div class="inv-music__disk">
                <svg class="inv-music__icon" id="inv-music-icon-play" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                <svg class="inv-music__icon hidden" id="inv-music-icon-pause" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
                </svg>
            </div>
        </button>
        <audio
            id="inv-audio"
            src="{{ $invitation->resolvedMusicUrl() }}"
            loop
            preload="metadata"
        ></audio>

        <main class="invitation-content" id="main-content">
            <section class="inv-welcome" id="inv-welcome" aria-label="Xush kelibsiz">
                <div class="inv-welcome__ornament" aria-hidden="true">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" d="M12 3c-1.5 2-4 4-4 7a4 4 0 108 0c0-3-2.5-5-4-7z"/>
                        <path stroke-linecap="round" d="M12 14v4M9 20h6"/>
                    </svg>
                </div>

                <p class="inv-welcome__pre">{{ $invitation->event_type }}</p>
                <h1 class="inv-welcome__names">
                    {{ $invitation->groom_name }}
                    <span class="inv-welcome__amp">&</span>
                    {{ $invitation->bride_name }}
                </h1>
                <p class="inv-welcome__date">{{ $invitation->welcomeSubtitle() }}</p>

                <button type="button" class="inv-welcome__scroll" id="inv-scroll-btn" aria-label="Taklifnomani ochish">
                    <span>Taklifnomani oching</span>
                    <span class="inv-welcome__scroll-icon" aria-hidden="true">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" d="M12 5v14M5 12l7 7 7-7"/>
                        </svg>
                    </span>
                </button>
            </section>

            <section class="inv-section" id="inv-details" aria-labelledby="inv-details-title">
                <div class="inv-reveal">
                    <p class="inv-section-label">Taklifnoma</p>
                    <h2 id="inv-details-title" class="inv-section-title">Sizni To'yingizga<br>Taklif Qilamiz</h2>

                    <div class="inv-text-card inv-glass">
                        <p>{{ $invitation->invitation_text_1 }}</p>
                        @if ($invitation->invitation_text_2)
                            <p>{{ $invitation->invitation_text_2 }}</p>
                        @endif
                        @if ($invitation->family_signature)
                            <p class="mt-4 font-serif text-inv-ink">
                                Oila nomidan — <em>{{ $invitation->family_signature }}</em>
                            </p>
                        @endif

                        <div
                            class="inv-countdown"
                            id="inv-countdown"
                            data-target="{{ $invitation->eventIsoString() }}"
                            role="timer"
                            aria-live="polite"
                        >
                            <div class="inv-countdown__item">
                                <span class="inv-countdown__value" id="cd-days">00</span>
                                <span class="inv-countdown__label">Kun</span>
                            </div>
                            <div class="inv-countdown__item">
                                <span class="inv-countdown__value" id="cd-hours">00</span>
                                <span class="inv-countdown__label">Soat</span>
                            </div>
                            <div class="inv-countdown__item">
                                <span class="inv-countdown__value" id="cd-minutes">00</span>
                                <span class="inv-countdown__label">Daqiqa</span>
                            </div>
                            <div class="inv-countdown__item">
                                <span class="inv-countdown__value" id="cd-seconds">00</span>
                                <span class="inv-countdown__label">Soniya</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="inv-section" id="inv-dresscode" aria-labelledby="inv-dress-title">
                <div class="inv-reveal">
                    <p class="inv-section-label">Kiyim Kodi</p>
                    <h2 id="inv-dress-title" class="inv-section-title">To'y Rang Palitrasi</h2>
                    <p class="mt-3 text-center text-sm text-inv-ink-soft px-2">
                        Marosimga mos ranglarda keling. Quyidagi palitraga amal qilishingizni iltimos qilamiz.
                    </p>

                    <div class="inv-dress-grid" id="inv-dress-grid" role="list">
                        @foreach ($invitation->dress_colors as $index => $color)
                            <button
                                type="button"
                                class="inv-dress-swatch {{ $index === 0 ? 'is-active' : '' }}"
                                role="listitem"
                                data-note="{{ $color['note'] }}"
                                aria-label="{{ $color['name'] }} rang"
                                aria-pressed="{{ $index === 0 ? 'true' : 'false' }}"
                            >
                                <span class="inv-dress-swatch__circle" style="background-color: {{ $color['hex'] }}"></span>
                                <span class="inv-dress-swatch__label">{{ $color['name'] }}</span>
                            </button>
                        @endforeach
                    </div>

                    <p class="inv-dress-note" id="inv-dress-note" aria-live="polite">
                        {{ $invitation->dress_colors[0]['note'] ?? '' }}
                    </p>
                </div>
            </section>

            <section class="inv-section" id="inv-rsvp" aria-labelledby="inv-rsvp-title">
                <div class="inv-reveal">
                    <p class="inv-section-label">Javob</p>
                    <h2 id="inv-rsvp-title" class="inv-section-title">Kelishni Tasdiqlash</h2>

                    <form
                        class="inv-rsvp-form inv-glass"
                        id="inv-rsvp-form"
                        data-rsvp-url="{{ route('rsvp.store', $invitation->slug) }}"
                        novalidate
                    >
                        @csrf
                        <div class="inv-rsvp-toggle" role="radiogroup" aria-label="Qatnashish holati">
                            <button type="button" class="inv-rsvp-option is-selected" data-status="attending" aria-pressed="true">
                                ✓ Kelaman
                            </button>
                            <button type="button" class="inv-rsvp-option inv-rsvp-option--decline" data-status="declined" aria-pressed="false">
                                ✕ Kela olmayman
                            </button>
                        </div>
                        <input type="hidden" name="status" id="rsvp-status" value="attending">

                        <div class="inv-field">
                            <label for="rsvp-name">Ismingiz</label>
                            <input type="text" id="rsvp-name" name="guest_name" placeholder="To'liq ismingiz" required autocomplete="name">
                        </div>

                        <div class="inv-field" id="rsvp-guests-field">
                            <label>Kattalar soni</label>
                            <div class="inv-counter" data-counter="adults" data-min="1" data-max="10">
                                <button type="button" class="inv-counter__btn" data-action="decrement" aria-label="Kamaytirish">−</button>
                                <span class="inv-counter__value" id="counter-adults">1</span>
                                <button type="button" class="inv-counter__btn" data-action="increment" aria-label="Oshirish">+</button>
                            </div>
                            <input type="hidden" name="adults_count" id="rsvp-adults" value="1">
                        </div>

                        <div class="inv-field" id="rsvp-children-field">
                            <label>Bolalar soni</label>
                            <div class="inv-counter" data-counter="children" data-min="0" data-max="8">
                                <button type="button" class="inv-counter__btn" data-action="decrement" aria-label="Kamaytirish">−</button>
                                <span class="inv-counter__value" id="counter-children">0</span>
                                <button type="button" class="inv-counter__btn" data-action="increment" aria-label="Oshirish">+</button>
                            </div>
                            <input type="hidden" name="children_count" id="rsvp-children" value="0">
                        </div>

                        <button type="submit" class="inv-submit" id="inv-submit">Tasdiqlash</button>
                    </form>

                    <div class="inv-rsvp-success inv-glass" id="inv-rsvp-success" aria-live="polite">
                        <div class="inv-rsvp-success__icon" aria-hidden="true">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="font-serif text-xl font-semibold text-inv-ink">Rahmat!</p>
                        <p class="mt-2 text-sm text-inv-ink-soft" id="inv-rsvp-success-msg">Javobingiz qabul qilindi.</p>
                    </div>
                </div>
            </section>

            <section class="inv-section" id="inv-location" aria-labelledby="inv-location-title">
                <div class="inv-reveal">
                    <p class="inv-section-label">Manzil</p>
                    <h2 id="inv-location-title" class="inv-section-title">Sana va Joy</h2>

                    <div class="inv-location-card inv-glass">
                        <p class="inv-location-card__venue">{{ $invitation->venue_name }}</p>
                        <p class="inv-location-card__address">{{ $invitation->venue_address }}</p>
                        <div class="inv-location-card__datetime">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $invitation->formattedEventDate() }} · {{ $invitation->formattedEventTime() }}
                        </div>

                        <div class="inv-map-preview" aria-hidden="true">
                            <div class="inv-map-preview__grid"></div>
                            <svg class="inv-map-preview__pin h-10 w-10" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </div>

                        @if ($invitation->googleMapsUrl())
                            <button type="button" class="inv-map-btn" id="inv-map-open">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                Xaritada ko'rish
                            </button>
                        @endif
                    </div>
                </div>
            </section>

            <footer class="inv-footer inv-reveal">
                <p>{{ $invitation->coupleTitle() }} · {{ $invitation->event_at->format('Y') }}</p>
                <p class="mt-1 text-xs opacity-70">Taklifnoma Premium</p>
            </footer>
        </main>

        @if ($invitation->googleMapsUrl())
            <div class="inv-map-modal" id="inv-map-modal" aria-hidden="true" role="dialog" aria-labelledby="inv-map-modal-title">
                <div class="inv-map-modal__sheet">
                    <h3 id="inv-map-modal-title" class="font-serif text-lg font-semibold text-inv-ink text-center">Navigatsiya</h3>
                    <p class="mt-2 text-center text-sm text-inv-ink-soft">{{ $invitation->venue_name }}, {{ $invitation->event_city }}</p>
                    <div class="inv-map-modal__actions">
                        <a href="{{ $invitation->googleMapsUrl() }}" target="_blank" rel="noopener noreferrer" class="inv-map-modal__link">
                            Google Maps
                        </a>
                        @if ($invitation->yandexMapsUrl())
                            <a href="{{ $invitation->yandexMapsUrl() }}" target="_blank" rel="noopener noreferrer" class="inv-map-modal__link" style="background: linear-gradient(135deg, #fc3f1d, #ff6b4a); color: white;">
                                Yandex Xarita
                            </a>
                        @endif
                        <button type="button" class="inv-map-modal__close" id="inv-map-close">Yopish</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
