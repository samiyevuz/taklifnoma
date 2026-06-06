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

    @if ($invitation?->isPublished())
        <x-invitation.share-bar :invitation="$invitation" class="builder-share-bar" />
    @endif

    @if ($invitation && ($rsvpSnapshot ?? null))
        <x-rsvp.live-panel
            :invitation="$invitation"
            :snapshot="$rsvpSnapshot"
            :poll-url="route('builder.rsvp.live', $invitation)"
        />
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
                enctype="multipart/form-data"
                novalidate
            >
                @csrf
                @if ($b['method'] !== 'POST') @method($b['method']) @endif
                <input type="hidden" name="dress_colors_json" id="dress_colors_json" value="">
                <input type="hidden" name="rsvp_enabled" id="rsvp_enabled" value="1">
                <input type="hidden" name="publish" id="publish_flag" value="0">
                <input type="hidden" name="template" id="template_blade" value="{{ old('template', $b['template_blade'] ?? 'nikoh-premium') }}">
                <input type="hidden" name="template_slug" id="template_slug" value="{{ $b['template_slug'] ?? 'nikoh' }}">
                <input type="hidden" name="template_variant" id="template_variant" value="{{ old('template_variant', $b['template_variant'] ?? '') }}">
                @if ($invitation)
                    <input type="hidden" name="invitation_id" id="invitation_id" value="{{ $invitation->id }}">
                @endif

                {{-- STEP 1: General --}}
                <div class="builder-step is-active" data-step="1">
                    <div class="builder-tabs" role="tablist" aria-label="{{ __('builder.tabs_label') }}">
                        <button type="button" class="builder-tab is-active" role="tab" aria-selected="true" data-tab="general">{{ __('builder.tab_general') }}</button>
                    </div>

                    <div class="builder-tab-panel is-active glass-luxury" data-tab-panel="general" role="tabpanel">
                        <h2 class="builder-panel-title">{{ __('builder.tab_general') }}</h2>
                        <p class="builder-panel-desc">{{ __('builder.tab_general_desc') }}</p>

                        <x-builder.profile-fields :schema="$b['field_schema']" />

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

                        <label class="builder-upload-zone" id="cover-upload-zone" for="cover_image">
                            <input type="file" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/webp" class="sr-only">
                            <div class="builder-upload-zone__icon" aria-hidden="true">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="builder-upload-zone__title">{{ __('builder.cover_upload') }}</p>
                            <p class="builder-upload-zone__hint">{{ __('builder.cover_upload_hint') }}</p>
                            <p class="builder-upload-zone__filename hidden" id="cover-filename"></p>
                            <img src="" alt="" class="builder-upload-preview hidden" id="cover-upload-preview">
                        </label>
                        @if (!empty($b['cover_image_url']))
                            <p class="builder-field-hint mt-2">{{ __('builder.cover_current') }}</p>
                            <img src="{{ $b['cover_image_url'] }}" alt="" class="builder-upload-preview mt-2" id="cover-current-preview">
                        @endif

                        @php
                            $storySlots = collect($b['story_gallery_slots'] ?? []);
                            $storyPortraits = $storySlots->where('type', 'portrait')->values();
                            $storyMoments = $storySlots->where('type', 'moment')->values();
                            $storyImagesBySlot = collect($b['story_images'] ?? [])->keyBy('slot');
                        @endphp
                        <div class="builder-story-gallery hidden mt-5" id="builder-story-gallery-wrap">
                            <div class="builder-story-gallery__head">
                                <h3 class="builder-story-gallery__title">{{ $b['story_gallery_title'] ?? __('builder.story_gallery_title') }}</h3>
                                <p class="builder-story-gallery__desc">{{ $b['story_gallery_subtitle'] ?? __('builder.story_gallery_desc') }}</p>
                            </div>

                            @if ($storyPortraits->isNotEmpty())
                                <div class="builder-story-grid builder-story-grid--portraits" id="builder-story-portraits">
                                    @foreach ($storyPortraits as $slot)
                                        @php $existing = $storyImagesBySlot->get($slot['key'], []); @endphp
                                        <div class="builder-story-card" data-story-slot="{{ $slot['key'] }}" data-story-type="{{ $slot['type'] }}">
                                            <label class="builder-story-picker" for="story_image_{{ $slot['key'] }}">
                                                <input
                                                    type="file"
                                                    id="story_image_{{ $slot['key'] }}"
                                                    name="story_image_{{ $slot['key'] }}"
                                                    accept="image/jpeg,image/png,image/webp"
                                                    class="sr-only"
                                                >
                                                <span class="builder-story-picker__thumb {{ empty($existing['url']) ? 'is-empty' : '' }}">
                                                    <img
                                                        src="{{ $existing['url'] ?? '' }}"
                                                        alt=""
                                                        class="builder-story-picker__img {{ empty($existing['url']) ? 'hidden' : '' }}"
                                                        id="story-preview-{{ $slot['key'] }}"
                                                    >
                                                    <span class="builder-story-picker__placeholder" aria-hidden="true">
                                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                                                    </span>
                                                </span>
                                                <span class="builder-story-picker__meta">
                                                    <span class="builder-story-picker__label">{{ $slot['label'] }}</span>
                                                    <span class="builder-story-picker__action">{{ __('builder.story_upload') }}</span>
                                                    <span class="builder-story-picker__hint">{{ $slot['hint'] ?? __('builder.story_portrait_hint') }}</span>
                                                    <span class="builder-story-picker__filename hidden" id="story-filename-{{ $slot['key'] }}"></span>
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if ($storyMoments->isNotEmpty())
                                <div class="builder-story-moments" id="builder-story-moments">
                                    <p class="builder-story-moments__label">{{ __('builder.story_moments_title') }}</p>
                                    <p class="builder-story-moments__hint">{{ __('builder.story_moments_limit', ['max' => \App\Support\StoryGallerySlots::MOMENT_COUNT]) }}</p>
                                    @foreach ($storyMoments as $slot)
                                        @php $existing = $storyImagesBySlot->get($slot['key'], []); @endphp
                                        <div class="builder-story-card builder-story-card--moment" data-story-slot="{{ $slot['key'] }}" data-story-type="{{ $slot['type'] }}">
                                            <label class="builder-story-picker builder-story-picker--moment" for="story_image_{{ $slot['key'] }}">
                                                <input
                                                    type="file"
                                                    id="story_image_{{ $slot['key'] }}"
                                                    name="story_image_{{ $slot['key'] }}"
                                                    accept="image/jpeg,image/png,image/webp"
                                                    class="sr-only"
                                                >
                                                <span class="builder-story-picker__thumb builder-story-picker__thumb--square {{ empty($existing['url']) ? 'is-empty' : '' }}">
                                                    <img
                                                        src="{{ $existing['url'] ?? '' }}"
                                                        alt=""
                                                        class="builder-story-picker__img {{ empty($existing['url']) ? 'hidden' : '' }}"
                                                        id="story-preview-{{ $slot['key'] }}"
                                                    >
                                                    <span class="builder-story-picker__placeholder" aria-hidden="true">
                                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                                                    </span>
                                                </span>
                                                <span class="builder-story-picker__meta">
                                                    <span class="builder-story-picker__label">{{ $slot['label'] }}</span>
                                                    <span class="builder-story-picker__action">{{ __('builder.story_upload') }}</span>
                                                    <span class="builder-story-picker__filename hidden" id="story-filename-{{ $slot['key'] }}"></span>
                                                </span>
                                            </label>
                                            <div class="builder-field builder-field--float builder-story-caption">
                                                <input
                                                    type="text"
                                                    id="story_caption_{{ $slot['key'] }}"
                                                    name="story_caption_{{ $slot['key'] }}"
                                                    value="{{ $existing['caption'] ?? '' }}"
                                                    placeholder=" "
                                                    maxlength="120"
                                                >
                                                <label for="story_caption_{{ $slot['key'] }}">{{ __('builder.story_caption') }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
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

                        <div class="builder-field mt-4 hidden" id="music-file-wrap">
                            <label for="music_file" class="builder-field-label">{{ __('builder.music_file') }}</label>
                            <input type="file" id="music_file" name="music_file" accept="audio/mpeg,audio/mp3,audio/mp4,audio/aac,audio/ogg,audio/wav,.mp3,.m4a,.aac,.ogg,.wav" class="builder-input w-full">
                            <p class="builder-field-hint">{{ __('builder.music_file_hint') }}</p>
                            <p class="builder-upload-zone__filename hidden" id="music-filename"></p>
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

                        <div class="mt-6">
                            <p class="builder-field-label">{{ __('builder.map_title') }}</p>
                            <p class="builder-panel-desc mb-3">{{ __('builder.map_desc') }}</p>
                            <button type="button" class="btn-outline-luxury text-sm mb-3" id="map-geocode-btn">{{ __('builder.map_geocode') }}</button>
                            <div id="builder-map-picker" class="builder-map-picker" aria-label="{{ __('builder.map_title') }}"></div>
                            <div class="builder-grid-2 mt-3">
                                <div class="builder-field builder-field--float">
                                    <input type="number" step="any" id="map_lat" name="map_lat" value="{{ old('map_lat', $b['map_lat']) }}" placeholder=" " data-preview-input>
                                    <label for="map_lat">{{ __('builder.map_lat') }}</label>
                                </div>
                                <div class="builder-field builder-field--float">
                                    <input type="number" step="any" id="map_lng" name="map_lng" value="{{ old('map_lng', $b['map_lng']) }}" placeholder=" " data-preview-input>
                                    <label for="map_lng">{{ __('builder.map_lng') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="builder-field mt-4" id="builder-slug-wrap">
                            <label class="builder-field-label" for="slug">{{ __('builder.slug') }}</label>
                            <p class="builder-field-explainer">{{ __('builder.slug_explainer') }}</p>
                            <div class="builder-composite-input">
                                <span class="builder-composite-input__affix" id="builder-slug-prefix">{{ ($b['slug_host'] ?? 'taklifnoma.net') }}/l/</span>
                                <input
                                    type="text"
                                    id="slug"
                                    name="slug"
                                    value="{{ old('slug', $b['slug'] ?? '') }}"
                                    pattern="[a-z0-9\-]+"
                                    placeholder="{{ __('builder.slug_placeholder') }}"
                                    autocomplete="off"
                                >
                            </div>
                            <p class="builder-url-preview" id="slug-url-preview" aria-live="polite"></p>
                            <p class="builder-field-hint" id="builder-slug-hint">{{ __('builder.slug_plan_hint') }}</p>
                        </div>

                        <div class="builder-plan-notice glass-luxury mt-4" id="builder-plan-notice" aria-live="polite"></div>

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
        <div class="builder-checkout-modal__dialog builder-checkout-modal__dialog--premium" role="dialog" aria-labelledby="checkout-title">
            <button type="button" class="builder-checkout-modal__close" data-close-checkout aria-label="{{ __('builder.close_checkout') }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
            </button>

            <div class="builder-checkout-modal__hero">
                <p class="section-label mb-2">{{ __('builder.checkout_label') }}</p>
                <h2 id="checkout-title" class="font-serif text-2xl font-semibold text-ink">{{ __('builder.checkout_title') }}</h2>
                <p class="mt-2 text-sm text-ink-soft">{{ __('builder.checkout_desc') }}</p>
            </div>

            <div class="builder-checkout-summary glass-luxury mt-5">
                <div class="builder-checkout-summary__row">
                    <span>{{ __('builder.checkout_template') }}</span>
                    <strong id="checkout-template">{{ $b['template_title'] }}</strong>
                </div>
                <div class="builder-checkout-summary__row">
                    <span id="checkout-subject-label">{{ $b['field_schema']['preview']['review_label'] ?? __('builder.checkout_couple') }}</span>
                    <strong id="checkout-couple"></strong>
                </div>
                <div class="builder-checkout-summary__row">
                    <span>{{ __('builder.checkout_url') }}</span>
                    <strong id="checkout-url" class="builder-checkout-summary__url"></strong>
                </div>
                <div class="builder-checkout-summary__row">
                    <span>{{ __('builder.checkout_event') }}</span>
                    <strong id="checkout-event"></strong>
                </div>
                <div class="builder-checkout-summary__total">
                    <span>{{ __('builder.checkout_total') }}</span>
                    <div class="text-right">
                        <strong id="checkout-price">{{ number_format($b['price_amount'], 0, '.', ' ') }} {{ $b['currency'] }}</strong>
                        @if ($b['payments']['complimentary'] ?? false)
                            <p class="mt-1 text-xs font-semibold text-luxury-emerald">{{ __('builder.complimentary_price_note') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            @if ($b['payments']['complimentary'] ?? false)
                <div class="builder-checkout-complimentary glass-luxury mt-5">
                    <p class="text-sm font-semibold text-luxury-emerald">{{ __('builder.complimentary_title') }}</p>
                    <p class="mt-1 text-sm text-ink-soft">{{ __('builder.complimentary_desc') }}</p>
                </div>
            @else
            <div class="builder-checkout-methods mt-5">
                <p class="builder-checkout-methods__label">{{ __('builder.payment_method') }}</p>
                <div class="builder-checkout-methods__grid" role="radiogroup" aria-label="{{ __('builder.payment_method') }}">
                    <x-builder.payment-method-card
                        id="payment_click"
                        name="payment_provider"
                        value="click"
                        :label="__('builder.payment_click')"
                        :hint="__('builder.payment_click_hint')"
                        :checked="true"
                    >
                        <svg viewBox="0 0 64 24" aria-hidden="true">
                            <rect x="1" y="3" width="62" height="18" rx="9" fill="#00A2FF"/>
                            <text x="32" y="16.5" text-anchor="middle" fill="#fff" font-size="9" font-weight="700" font-family="Inter, Arial, sans-serif">CLICK</text>
                        </svg>
                    </x-builder.payment-method-card>

                    <x-builder.payment-method-card
                        id="payment_payme"
                        name="payment_provider"
                        value="payme"
                        :label="__('builder.payment_payme')"
                        :hint="__('builder.payment_payme_hint')"
                    >
                        <svg viewBox="0 0 64 24" aria-hidden="true">
                            <rect x="1" y="3" width="62" height="18" rx="9" fill="#10B981"/>
                            <text x="32" y="16.5" text-anchor="middle" fill="#fff" font-size="8.5" font-weight="700" font-family="Inter, Arial, sans-serif">PAYME</text>
                        </svg>
                    </x-builder.payment-method-card>
                </div>
            </div>
            @endif

            <div class="builder-checkout-actions mt-6">
                <button type="button" class="btn-gold-shimmer btn-shine w-full" id="checkout-pay-btn" data-ripple
                    data-label-pay="{{ __('builder.checkout_pay_activate') }}"
                    data-label-free="{{ __('builder.complimentary_activate') }}">
                    {{ ($b['payments']['complimentary'] ?? false) ? __('builder.complimentary_activate') : __('builder.checkout_pay_activate') }}
                </button>
                <button type="button" class="btn-outline-luxury w-full" data-checkout-action="draft">{{ __('builder.save_draft') }}</button>
            </div>

            <p class="builder-checkout-alert hidden" id="checkout-alert" role="alert"></p>
            @if (!($b['payments']['complimentary'] ?? false))
                <p class="mt-3 text-center text-xs text-ink-muted">{{ __('builder.checkout_secure_note') }}</p>
            @endif
        </div>
    </div>
</div>

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endpush
