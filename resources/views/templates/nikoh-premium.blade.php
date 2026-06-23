@extends('layouts.invitation')

@section('body')
    @php
        $coverUrl = $invitation->resolvedCoverUrl();
        $tierRibbon = $invitation->presentationTierRibbon();
    @endphp
    <div
        class="invitation-page inv-presentation {{ $invitation->presentationThemeClass() }} {{ $invitation->presentationAnimationClass() }}"
        id="invitation-app"
        data-invitation-slug="{{ $invitation->slug }}"
        data-animation-tier="{{ $invitation->entitlements()['animation'] ?? 'enhanced' }}"
    >
        <div class="builder-preview-fx" aria-hidden="true">
            @if ($coverUrl)
                <div
                    class="builder-preview-cover"
                    style="background-image: url('{{ $coverUrl }}'); background-position: {{ $invitation->presentationCoverFocus() }};"
                ></div>
            @endif
            <div class="builder-preview-overlay"></div>
            <div class="builder-preview-shimmer"></div>
            <div class="builder-preview-particles" id="inv-particles"></div>
        </div>

        @if ($tierRibbon)
            <span class="builder-preview-tier-ribbon">{{ $tierRibbon }}</span>
        @endif

        <x-invitation.chrome :invitation="$invitation" />

        <main class="invitation-content" id="main-content">
            <section class="inv-welcome" id="inv-welcome" aria-label="{{ __('invitation.welcome_aria') }}">
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

                <button type="button" class="inv-welcome__scroll" id="inv-scroll-btn" aria-label="{{ __('invitation.open_invitation') }}">
                    <span>{{ __('invitation.open_invitation') }}</span>
                    <span class="inv-welcome__scroll-icon" aria-hidden="true">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" d="M12 5v14M5 12l7 7 7-7"/>
                        </svg>
                    </span>
                </button>
            </section>

            <section class="inv-section" id="inv-details" aria-labelledby="inv-details-title">
                <div class="inv-reveal">
                    <p class="inv-section-label">{{ __('invitation.section_invitation') }}</p>
                    <h2 id="inv-details-title" class="inv-section-title">{!! __('invitation.section_invitation_title') !!}</h2>

                    <div class="inv-text-card inv-glass">
                        <p>{{ $invitation->invitation_text_1 }}</p>
                        @if ($invitation->invitation_text_2)
                            <p>{{ $invitation->invitation_text_2 }}</p>
                        @endif
                        @if ($invitation->family_signature)
                            <p class="mt-4 font-serif text-inv-ink">
                                {{ __('invitation.family_from') }} <em>{{ $invitation->family_signature }}</em>
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
                                <span class="inv-countdown__label">{{ __('invitation.countdown_days') }}</span>
                            </div>
                            <div class="inv-countdown__item">
                                <span class="inv-countdown__value" id="cd-hours">00</span>
                                <span class="inv-countdown__label">{{ __('invitation.countdown_hours') }}</span>
                            </div>
                            <div class="inv-countdown__item">
                                <span class="inv-countdown__value" id="cd-minutes">00</span>
                                <span class="inv-countdown__label">{{ __('invitation.countdown_minutes') }}</span>
                            </div>
                            <div class="inv-countdown__item">
                                <span class="inv-countdown__value" id="cd-seconds">00</span>
                                <span class="inv-countdown__label">{{ __('invitation.countdown_seconds') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @if ($invitation->hasStoryImages())
                @php
                    $storyItems = collect($invitation->resolvedStoryGallery())->filter(fn (array $item) => filled($item['url'] ?? null));
                    $portraits = $storyItems->where('type', 'portrait')->values();
                    $moments = $storyItems->where('type', 'moment')->values();
                @endphp
                <section class="inv-section inv-story" id="inv-story" aria-labelledby="inv-story-title">
                    <header class="inv-story__head inv-story-reveal">
                        <p class="inv-section-label">{{ __('invitation.story_label') }}</p>
                        <h2 id="inv-story-title" class="inv-section-title">{{ $invitation->storyGalleryTitle() }}</h2>
                        <p class="inv-story__subtitle">{{ $invitation->storyGallerySubtitle() }}</p>
                    </header>

                    @if ($portraits->isNotEmpty())
                        <div class="inv-story-duo inv-story-reveal" data-story-stage="duo">
                            @foreach ($portraits as $index => $portrait)
                                @php
                                    $portraitName = $invitation->storyPortraitName($portrait['slot']) ?: $portrait['label'];
                                @endphp
                                <figure class="inv-story-duo__card" style="--story-i: {{ $index }}" data-story-memory>
                                    <div class="inv-story-polaroid inv-story-polaroid--hero">
                                        <div class="inv-story-polaroid__photo">
                                            <img
                                                src="{{ $portrait['url'] }}"
                                                alt="{{ $portraitName }}"
                                                loading="lazy"
                                                decoding="async"
                                                width="280"
                                                height="340"
                                            >
                                        </div>
                                        <figcaption class="inv-story-polaroid__caption inv-story-polaroid__caption--name">
                                            {{ $portraitName }}
                                        </figcaption>
                                    </div>
                                </figure>
                            @endforeach
                            @if ($portraits->count() > 1)
                                <span class="inv-story-duo__bond" aria-hidden="true">&</span>
                            @endif
                        </div>
                    @endif

                    @if ($moments->isNotEmpty())
                        <div class="inv-story-album inv-story-reveal" data-story-stage="album">
                            <div class="inv-story-album__divider" aria-hidden="true">
                                <span class="inv-story-album__line"></span>
                                <span class="inv-story-album__label">{{ __('invitation.story_memories_label') }}</span>
                                <span class="inv-story-album__line"></span>
                            </div>
                            <div class="inv-story-memories">
                                @foreach ($moments as $index => $moment)
                                    <article
                                        class="inv-story-polaroid inv-story-polaroid--memory"
                                        style="--story-i: {{ $index }}; --story-rot: {{ $index % 2 === 0 ? '-1.25deg' : '1.25deg' }}"
                                        data-story-memory
                                    >
                                        <span class="inv-story-polaroid__index" aria-hidden="true">{{ $index + 1 }}</span>
                                        <div class="inv-story-polaroid__photo">
                                            <img
                                                src="{{ $moment['url'] }}"
                                                alt="{{ $moment['caption'] ?: $moment['label'] }}"
                                                loading="lazy"
                                                decoding="async"
                                                width="240"
                                                height="240"
                                            >
                                        </div>
                                        <div class="inv-story-polaroid__caption">
                                            @if (filled($moment['caption']))
                                                <p>{{ $moment['caption'] }}</p>
                                            @else
                                                <p class="is-placeholder">{{ $moment['label'] }}</p>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </section>
            @endif

            <section class="inv-section" id="inv-dresscode" aria-labelledby="inv-dress-title">
                <div class="inv-reveal">
                    <p class="inv-section-label">{{ __('invitation.dress_code') }}</p>
                    <h2 id="inv-dress-title" class="inv-section-title">{{ __('invitation.dress_code_title') }}</h2>
                    <p class="mt-3 text-center text-sm text-inv-ink-soft px-2">{{ __('invitation.dress_code_desc') }}</p>

                    <div class="inv-dress-grid" id="inv-dress-grid" role="list">
                        @foreach ($invitation->dress_colors as $index => $color)
                            <button
                                type="button"
                                class="inv-dress-swatch {{ $index === 0 ? 'is-active' : '' }}"
                                role="listitem"
                                data-note="{{ $color['note'] }}"
                                aria-label="{{ $color['name'] }} {{ __('invitation.dress_color_suffix') }}"
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

            @if ($invitation->rsvp_enabled)
            <section class="inv-section" id="inv-rsvp" aria-labelledby="inv-rsvp-title">
                <div class="inv-reveal">
                    <p class="inv-section-label">{{ __('invitation.rsvp') }}</p>
                    <h2 id="inv-rsvp-title" class="inv-section-title">{{ __('invitation.rsvp_title') }}</h2>

                    @php
                        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
                        $rsvpUrl = $appHost && request()->getHost() !== $appHost
                            ? url('/rsvp')
                            : route('rsvp.store', $invitation->slug);
                        $guestLimit = $invitation->resolvedGuestLimit();
                    @endphp
                    <form
                        class="inv-rsvp-form inv-glass"
                        id="inv-rsvp-form"
                        data-rsvp-url="{{ $rsvpUrl }}"
                        @if ($guestLimit) data-guest-limit="{{ $guestLimit }}" @endif
                        novalidate
                    >
                        @csrf
                        <div class="inv-rsvp-toggle" role="radiogroup" aria-label="{{ __('invitation.rsvp_status_aria') }}">
                            <button type="button" class="inv-rsvp-option is-selected" data-status="attending" aria-pressed="true">
                                {{ __('invitation.rsvp_attending') }}
                            </button>
                            <button type="button" class="inv-rsvp-option inv-rsvp-option--decline" data-status="declined" aria-pressed="false">
                                {{ __('invitation.rsvp_declined') }}
                            </button>
                        </div>
                        <input type="hidden" name="status" id="rsvp-status" value="attending">

                        <div class="inv-field">
                            <label for="rsvp-name">{{ __('invitation.rsvp_name') }}</label>
                            <input type="text" id="rsvp-name" name="guest_name" placeholder="{{ __('invitation.rsvp_name_placeholder') }}" required autocomplete="name">
                        </div>

                        <div class="inv-field" id="rsvp-guests-field">
                            <label>{{ __('invitation.rsvp_adults') }}</label>
                            <div class="inv-counter" data-counter="adults" data-min="1" data-max="10">
                                <button type="button" class="inv-counter__btn" data-action="decrement" aria-label="{{ __('invitation.decrement') }}">−</button>
                                <span class="inv-counter__value" id="counter-adults">1</span>
                                <button type="button" class="inv-counter__btn" data-action="increment" aria-label="{{ __('invitation.increment') }}">+</button>
                            </div>
                            <input type="hidden" name="adults_count" id="rsvp-adults" value="1">
                        </div>

                        <div class="inv-field" id="rsvp-children-field">
                            <label>{{ __('invitation.rsvp_children') }}</label>
                            <div class="inv-counter" data-counter="children" data-min="0" data-max="8">
                                <button type="button" class="inv-counter__btn" data-action="decrement" aria-label="{{ __('invitation.decrement') }}">−</button>
                                <span class="inv-counter__value" id="counter-children">0</span>
                                <button type="button" class="inv-counter__btn" data-action="increment" aria-label="{{ __('invitation.increment') }}">+</button>
                            </div>
                            <input type="hidden" name="children_count" id="rsvp-children" value="0">
                        </div>

                        <button type="submit" class="inv-submit" id="inv-submit">{{ __('invitation.rsvp_submit') }}</button>
                    </form>

                    <div class="inv-rsvp-success inv-glass" id="inv-rsvp-success" aria-live="polite">
                        <div class="inv-rsvp-success__icon" aria-hidden="true">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="font-serif text-xl font-semibold text-inv-ink" id="inv-rsvp-success-title">{{ __('invitation.rsvp_thanks') }}</p>
                        <p class="mt-2 text-sm text-inv-ink-soft" id="inv-rsvp-success-msg">{{ __('invitation.rsvp_success') }}</p>
                    </div>
                </div>
            </section>
            @endif

            <section class="inv-section" id="inv-location" aria-labelledby="inv-location-title">
                <div class="inv-reveal">
                    <p class="inv-section-label">{{ __('invitation.location') }}</p>
                    <h2 id="inv-location-title" class="inv-section-title">{{ __('invitation.location_title') }}</h2>

                    <div class="inv-location-card inv-glass">
                        <p class="inv-location-card__venue">{{ $invitation->venue_name }}</p>
                        <p class="inv-location-card__address">{{ $invitation->venue_address }}</p>
                        <div class="inv-location-card__datetime">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $invitation->formattedEventDate() }} · {{ $invitation->formattedEventTime() }}
                        </div>

                        @if ($invitation->map_lat && $invitation->map_lng)
                            <div
                                id="inv-location-map"
                                class="inv-location-map"
                                data-lat="{{ $invitation->map_lat }}"
                                data-lng="{{ $invitation->map_lng }}"
                                aria-label="{{ __('invitation.location_title') }}"
                            ></div>
                        @else
                            <div class="inv-map-preview" aria-hidden="true">
                                <div class="inv-map-preview__grid"></div>
                                <svg class="inv-map-preview__pin h-10 w-10" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                            </div>
                        @endif

                        @if ($invitation->googleMapsUrl())
                            <button type="button" class="inv-map-btn" id="inv-map-open">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                {{ __('invitation.view_map') }}
                            </button>
                        @endif
                    </div>
                </div>
            </section>

            <footer class="inv-footer inv-reveal">
                <p>{{ $invitation->coupleTitle() }} · {{ $invitation->event_at->format('Y') }}</p>
                <p class="mt-1 text-xs opacity-70">{{ __('invitation.footer_brand') }}</p>
            </footer>
        </main>

        @if ($invitation->googleMapsUrl())
            <div class="inv-map-modal" id="inv-map-modal" aria-hidden="true" role="dialog" aria-labelledby="inv-map-modal-title">
                <div class="inv-map-modal__sheet">
                    <h3 id="inv-map-modal-title" class="font-serif text-lg font-semibold text-inv-ink text-center">{{ __('invitation.navigation') }}</h3>
                    <p class="mt-2 text-center text-sm text-inv-ink-soft">{{ $invitation->venue_name }}, {{ $invitation->event_city }}</p>
                    <div class="inv-map-modal__actions">
                        <a href="{{ $invitation->googleMapsUrl() }}" target="_blank" rel="noopener noreferrer" class="inv-map-modal__link">
                            Google Maps
                        </a>
                        @if ($invitation->yandexMapsUrl())
                            <a href="{{ $invitation->yandexMapsUrl() }}" target="_blank" rel="noopener noreferrer" class="inv-map-modal__link" style="background: linear-gradient(135deg, #fc3f1d, #ff6b4a); color: white;">
                                {{ __('invitation.yandex_maps') }}
                            </a>
                        @endif
                        <button type="button" class="inv-map-modal__close" id="inv-map-close">{{ __('invitation.close') }}</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('head')
    @if ($invitation->map_lat && $invitation->map_lng)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @endif
@endpush
