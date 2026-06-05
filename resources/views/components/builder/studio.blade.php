@props(['bootstrap', 'invitation' => null, 'stats' => null])

@php
    $b = $bootstrap;
@endphp

<div
    id="builder-studio"
    class="builder-studio"
    data-bootstrap='@json($b)'
>
    <header class="builder-studio__header">
        <div class="builder-studio__header-main">
            <a href="{{ auth()->check() ? route('account.dashboard') : '/' }}" class="builder-studio__back" aria-label="{{ __('builder.back_dashboard') }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <p class="section-label mb-1">Konstruktor</p>
                <h1 class="font-serif text-xl font-semibold text-ink sm:text-2xl">
                    {{ $invitation ? $invitation->coupleTitle() : __('builder.create_title') }}
                </h1>
            </div>
        </div>
        <div class="builder-studio__header-tools">
            <x-ui.locale-switcher />
            @if ($invitation)
                <span class="builder-status {{ $invitation->isPublished() ? 'is-published' : 'is-draft' }}">
                    {{ $invitation->isPublished() ? __('builder.edit_published') : __('builder.edit_draft') }}
                </span>
            @endif
        </div>
    </header>

    @if ($invitation && $stats)
        <div class="builder-stats">
            <div class="builder-stats__item"><p class="builder-stats__value">{{ $stats['attending'] }}</p><p class="builder-stats__label">{{ __('builder.stats_attending') }}</p></div>
            <div class="builder-stats__item"><p class="builder-stats__value">{{ $stats['declined'] }}</p><p class="builder-stats__label">{{ __('builder.stats_declined') }}</p></div>
            <div class="builder-stats__item"><p class="builder-stats__value">{{ $stats['total_guests'] }}</p><p class="builder-stats__label">{{ __('builder.stats_guests') }}</p></div>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="builder-alert builder-alert--error" role="alert">
            <ul class="list-disc pl-5 space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if (session('success'))
        <div class="builder-alert builder-alert--success" role="status">{{ session('success') }}</div>
    @endif

    <nav class="builder-stepper" aria-label="{{ __('builder.stepper_label') }}">
        <ol class="builder-stepper__list">
            <li class="builder-stepper__item is-active" data-step-indicator="1">
                <span class="builder-stepper__num">1</span>
                <span class="builder-stepper__text">{{ __('builder.step_info') }}</span>
            </li>
            <li class="builder-stepper__item" data-step-indicator="2">
                <span class="builder-stepper__num">2</span>
                <span class="builder-stepper__text">{{ __('builder.step_design') }}</span>
            </li>
            <li class="builder-stepper__item" data-step-indicator="3">
                <span class="builder-stepper__num">3</span>
                <span class="builder-stepper__text">{{ __('builder.step_review') }}</span>
            </li>
        </ol>
    </nav>

    <div class="builder-studio__layout">
        <div class="builder-studio__panel">
            <form
                id="builder-form"
                action="{{ $b['action'] }}"
                method="POST"
                class="builder-form"
                novalidate
            >
                @csrf
                @if ($b['method'] !== 'POST') @method($b['method']) @endif
                <input type="hidden" name="dress_colors_json" id="dress_colors_json" value="">
                <input type="hidden" name="rsvp_enabled" id="rsvp_enabled" value="1">
                <input type="hidden" name="publish" id="publish_flag" value="0">

                {{-- STEP 1: General --}}
                <div class="builder-step is-active" data-step="1">
                    <div class="builder-tabs" role="tablist" aria-label="{{ __('builder.tabs_label') }}">
                        <button type="button" class="builder-tab is-active" role="tab" aria-selected="true" data-tab="general">{{ __('builder.tab_general') }}</button>
                    </div>

                    <div class="builder-tab-panel is-active glass-luxury" data-tab-panel="general" role="tabpanel">
                        <h2 class="builder-panel-title">{{ __('builder.tab_general') }}</h2>
                        <p class="builder-panel-desc">{{ __('builder.tab_general_desc') }}</p>

                        <div class="builder-grid-2">
                            <div class="builder-field builder-field--float">
                                <input type="text" id="groom_name" name="groom_name" value="{{ old('groom_name', $b['groom_name']) }}" placeholder=" " required data-preview-input>
                                <label for="groom_name">{{ __('builder.groom_name') }}</label>
                            </div>
                            <div class="builder-field builder-field--float">
                                <input type="text" id="bride_name" name="bride_name" value="{{ old('bride_name', $b['bride_name']) }}" placeholder=" " required data-preview-input>
                                <label for="bride_name">{{ __('builder.bride_name') }}</label>
                            </div>
                        </div>

                        <div class="builder-field builder-field--float mt-4">
                            <input type="text" id="event_type" name="event_type" value="{{ old('event_type', $b['event_type']) }}" placeholder=" " required data-preview-input>
                            <label for="event_type">{{ __('builder.event_type') }}</label>
                        </div>

                        <div class="builder-grid-2 mt-4">
                            <div class="builder-field builder-field--float">
                                <input type="datetime-local" id="event_at" name="event_at" value="{{ old('event_at', $b['event_at']) }}" placeholder=" " required data-preview-input>
                                <label for="event_at">{{ __('builder.event_at') }}</label>
                            </div>
                            <div class="builder-field builder-field--float">
                                <input type="text" id="event_city" name="event_city" value="{{ old('event_city', $b['event_city']) }}" placeholder=" " data-preview-input>
                                <label for="event_city">{{ __('builder.event_city') }}</label>
                            </div>
                        </div>

                        <div class="builder-field builder-field--float mt-4">
                            <input type="text" id="venue_name" name="venue_name" value="{{ old('venue_name', $b['venue_name']) }}" placeholder=" " required data-preview-input>
                            <label for="venue_name">{{ __('builder.venue_name') }}</label>
                        </div>

                        <div class="builder-field builder-field--float mt-4">
                            <input type="text" id="venue_address" name="venue_address" value="{{ old('venue_address', $b['venue_address']) }}" placeholder=" " required data-preview-input>
                            <label for="venue_address">{{ __('builder.venue_address') }}</label>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: Media & Music --}}
                <div class="builder-step" data-step="2" hidden>
                    <div class="builder-tabs" role="tablist">
                        <button type="button" class="builder-tab is-active" role="tab" aria-selected="true" data-tab="media">{{ __('builder.tab_media') }}</button>
                    </div>

                    <div class="builder-tab-panel is-active glass-luxury" data-tab-panel="media" role="tabpanel">
                        <h2 class="builder-panel-title">{{ __('builder.tab_media') }}</h2>
                        <p class="builder-panel-desc">{{ __('builder.tab_media_desc') }}</p>

                        <div class="builder-upload-zone" aria-label="{{ __('builder.cover_upload') }}">
                            <div class="builder-upload-zone__icon" aria-hidden="true">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="builder-upload-zone__title">{{ __('builder.cover_upload') }}</p>
                            <p class="builder-upload-zone__hint">{{ __('builder.cover_upload_hint') }}</p>
                        </div>

                        <div class="builder-field mt-5">
                            <label for="music_preset">{{ __('builder.music_preset') }}</label>
                            <select id="music_preset" class="builder-select">
                                @foreach ($b['music_presets'] as $preset)
                                    <option value="{{ $preset['id'] }}" data-url="{{ $preset['url'] }}">{{ $preset['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="builder-field builder-field--float mt-4" id="music-url-wrap">
                            <input type="url" id="music_url" name="music_url" value="{{ old('music_url', $b['music_url']) }}" placeholder=" ">
                            <label for="music_url">{{ __('builder.music_url') }}</label>
                            <p class="builder-field-hint">{!! __('builder.music_hint') !!}</p>
                        </div>

                        <div class="builder-field builder-field--float mt-4">
                            <textarea id="invitation_text_1" name="invitation_text_1" rows="4" placeholder=" " required data-preview-input>{{ old('invitation_text_1', $b['invitation_text_1']) }}</textarea>
                            <label for="invitation_text_1">{{ __('builder.invitation_text_1') }}</label>
                        </div>

                        <div class="builder-field builder-field--float mt-4">
                            <textarea id="invitation_text_2" name="invitation_text_2" rows="3" placeholder=" " data-preview-input>{{ old('invitation_text_2', $b['invitation_text_2']) }}</textarea>
                            <label for="invitation_text_2">{{ __('builder.invitation_text_2') }}</label>
                        </div>

                        <div class="builder-field builder-field--float mt-4">
                            <input type="text" id="family_signature" name="family_signature" value="{{ old('family_signature', $b['family_signature']) }}" placeholder=" " data-preview-input>
                            <label for="family_signature">{{ __('builder.family_signature') }}</label>
                        </div>
                    </div>
                </div>

                {{-- STEP 3: Advanced + Review --}}
                <div class="builder-step" data-step="3" hidden>
                    <div class="builder-tabs" role="tablist">
                        <button type="button" class="builder-tab is-active" role="tab" aria-selected="true" data-tab="advanced">{{ __('builder.tab_advanced') }}</button>
                    </div>

                    <div class="builder-tab-panel is-active glass-luxury" data-tab-panel="advanced" role="tabpanel">
                        <h2 class="builder-panel-title">{{ __('builder.tab_advanced') }}</h2>
                        <p class="builder-panel-desc">{{ __('builder.tab_advanced_desc') }}</p>

                        <div class="builder-toggle-row">
                            <div>
                                <p class="builder-toggle-row__title">{{ __('builder.rsvp_toggle') }}</p>
                                <p class="builder-toggle-row__desc">{{ __('builder.rsvp_toggle_desc') }}</p>
                            </div>
                            <button type="button" class="builder-switch is-on" id="rsvp-toggle" role="switch" aria-checked="true" aria-label="{{ __('builder.rsvp_toggle') }}">
                                <span class="builder-switch__thumb"></span>
                            </button>
                        </div>

                        <div class="mt-6">
                            <p class="builder-field-label">{{ __('builder.dress_palette') }}</p>
                            <p class="builder-panel-desc mb-3">{{ __('builder.dress_palette_desc') }}</p>
                            <div class="builder-color-palette" id="dress-palette" role="list"></div>
                        </div>

                        <div class="builder-grid-2 mt-6">
                            <div class="builder-field builder-field--float">
                                <input type="number" step="any" id="map_lat" name="map_lat" value="{{ old('map_lat', $b['map_lat']) }}" placeholder=" ">
                                <label for="map_lat">{{ __('builder.map_lat') }}</label>
                            </div>
                            <div class="builder-field builder-field--float">
                                <input type="number" step="any" id="map_lng" name="map_lng" value="{{ old('map_lng', $b['map_lng']) }}" placeholder=" ">
                                <label for="map_lng">{{ __('builder.map_lng') }}</label>
                            </div>
                        </div>

                        @if ($invitation)
                            <div class="builder-field builder-field--float mt-4">
                                <div class="builder-slug-field">
                                    <span class="builder-slug-prefix">/i/</span>
                                    <input type="text" id="slug" name="slug" value="{{ old('slug', $invitation->slug) }}" pattern="[a-z0-9\-]+" placeholder=" ">
                                </div>
                                <label for="slug">{{ __('builder.slug') }}</label>
                            </div>
                        @endif

                        <div class="builder-review glass-luxury mt-6" id="builder-review">
                            <h3 class="builder-review__title">{{ __('builder.review_title') }}</h3>
                            <dl class="builder-review__list"></dl>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <aside class="builder-studio__preview-col" aria-hidden="false">
            <div class="builder-preview-stage">
                <x-builder.preview-panel />
            </div>
        </aside>
    </div>

    <div class="builder-action-bar" id="builder-action-bar">
        <button type="button" class="btn-outline-luxury builder-action-bar__back" id="builder-back" disabled>{{ __('builder.back') }}</button>
        <button type="button" class="btn-gold-shimmer btn-shine builder-action-bar__next" id="builder-next" data-ripple data-continue-label="{{ __('builder.continue') }}" data-save-label="{{ __('builder.save') }}">{{ __('builder.continue') }}</button>
    </div>

    <button type="button" class="builder-preview-fab lg:hidden" id="builder-preview-fab" aria-label="{{ __('builder.preview_fab') }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        <span>{{ __('builder.preview_fab') }}</span>
    </button>

    <div class="builder-preview-sheet lg:hidden" id="builder-preview-sheet" aria-hidden="true">
        <div class="builder-preview-sheet__backdrop" data-close-preview-sheet></div>
        <div class="builder-preview-sheet__panel" role="dialog" aria-label="{{ __('builder.live_preview') }}">
            <div class="builder-preview-sheet__header">
                <h2 class="font-serif text-lg font-semibold text-ink">{{ __('builder.live_preview') }}</h2>
                <button type="button" class="nav-icon-btn" data-close-preview-sheet aria-label="{{ __('builder.close_preview') }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
            <div class="builder-preview-sheet__body" id="builder-preview-sheet-mount"></div>
        </div>
    </div>

    <div class="builder-checkout-modal" id="builder-checkout-modal" aria-hidden="true">
        <div class="builder-checkout-modal__backdrop" data-close-checkout></div>
        <div class="builder-checkout-modal__dialog" role="dialog" aria-labelledby="checkout-title">
            <button type="button" class="builder-checkout-modal__close" data-close-checkout aria-label="{{ __('builder.close_checkout') }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
            <p class="section-label mb-2">{{ __('builder.checkout_label') }}</p>
            <h2 id="checkout-title" class="font-serif text-2xl font-semibold text-ink">{{ __('builder.checkout_title') }}</h2>
            <p class="mt-2 text-sm text-ink-soft">{{ __('builder.checkout_desc') }}</p>

            <div class="builder-checkout-summary glass-luxury mt-6">
                <div class="builder-checkout-summary__row">
                    <span>{{ __('builder.checkout_template') }}</span>
                    <strong id="checkout-template">{{ $b['template_title'] }}</strong>
                </div>
                <div class="builder-checkout-summary__row">
                    <span>{{ __('builder.checkout_couple') }}</span>
                    <strong id="checkout-couple"></strong>
                </div>
                <div class="builder-checkout-summary__row">
                    <span>{{ __('builder.checkout_event') }}</span>
                    <strong id="checkout-event"></strong>
                </div>
                <div class="builder-checkout-summary__total">
                    <span>{{ __('builder.checkout_total') }}</span>
                    <strong id="checkout-price">{{ number_format($b['price_amount'], 0, '.', ' ') }} {{ $b['currency'] }}</strong>
                </div>
            </div>

            <div class="builder-checkout-actions mt-6">
                <button type="button" class="btn-outline-luxury w-full" data-checkout-action="draft">{{ __('builder.save_draft') }}</button>
                <button type="button" class="btn-gold-shimmer btn-shine w-full" data-checkout-action="publish" data-ripple>{{ __('builder.checkout_pay_publish') }}</button>
            </div>
            <p class="mt-4 text-center text-xs text-ink-muted">{{ __('builder.checkout_note') }}</p>
        </div>
    </div>
</div>
