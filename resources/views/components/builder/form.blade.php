@props(['invitation' => null, 'defaults' => [], 'action', 'method' => 'POST'])

@php
    $data = $invitation ?? (object) $defaults;
    $eventAt = old('event_at', isset($data->event_at)
        ? (\Carbon\Carbon::parse($data->event_at)->format('Y-m-d\TH:i'))
        : now()->addMonths(3)->format('Y-m-d\TH:i'));
@endphp

<form action="{{ $action }}" method="POST" class="builder-form space-y-8">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <fieldset class="builder-section glass-luxury rounded-2xl p-6">
        <legend class="builder-section__title font-serif text-xl font-semibold text-ink mb-4">{{ __('builder.section_couple') }}</legend>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="builder-field"><label for="groom_name">{{ __('builder.groom_name') }}</label><input type="text" id="groom_name" name="groom_name" value="{{ old('groom_name', $data->groom_name ?? '') }}" required></div>
            <div class="builder-field"><label for="bride_name">{{ __('builder.bride_name') }}</label><input type="text" id="bride_name" name="bride_name" value="{{ old('bride_name', $data->bride_name ?? '') }}" required></div>
        </div>
        <div class="builder-field mt-4"><label for="event_type">{{ __('builder.event_type') }}</label><input type="text" id="event_type" name="event_type" value="{{ old('event_type', $data->event_type ?? __('builder.default_event_type')) }}" required></div>
    </fieldset>

    <fieldset class="builder-section glass-luxury rounded-2xl p-6">
        <legend class="builder-section__title font-serif text-xl font-semibold text-ink mb-4">{{ __('builder.section_event') }}</legend>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="builder-field"><label for="event_at">{{ __('builder.event_at') }}</label><input type="datetime-local" id="event_at" name="event_at" value="{{ $eventAt }}" required></div>
            <div class="builder-field"><label for="event_city">{{ __('builder.event_city') }}</label><input type="text" id="event_city" name="event_city" value="{{ old('event_city', $data->event_city ?? __('builder.default_city')) }}"></div>
        </div>
        <div class="builder-field mt-4"><label for="venue_name">{{ __('builder.venue_name') }}</label><input type="text" id="venue_name" name="venue_name" value="{{ old('venue_name', $data->venue_name ?? '') }}" required></div>
        <div class="builder-field mt-4"><label for="venue_address">{{ __('builder.venue_address') }}</label><input type="text" id="venue_address" name="venue_address" value="{{ old('venue_address', $data->venue_address ?? '') }}" required></div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-4">
            <div class="builder-field"><label for="map_lat">{{ __('builder.map_lat') }}</label><input type="number" step="any" id="map_lat" name="map_lat" value="{{ old('map_lat', $data->map_lat ?? '') }}" placeholder="41.311081"></div>
            <div class="builder-field"><label for="map_lng">{{ __('builder.map_lng') }}</label><input type="number" step="any" id="map_lng" name="map_lng" value="{{ old('map_lng', $data->map_lng ?? '') }}" placeholder="69.240562"></div>
        </div>
    </fieldset>

    <fieldset class="builder-section glass-luxury rounded-2xl p-6">
        <legend class="builder-section__title font-serif text-xl font-semibold text-ink mb-4">{{ __('builder.section_text') }}</legend>
        <div class="builder-field"><label for="invitation_text_1">{{ __('builder.invitation_text_1') }}</label><textarea id="invitation_text_1" name="invitation_text_1" rows="4" required>{{ old('invitation_text_1', $data->invitation_text_1 ?? '') }}</textarea></div>
        <div class="builder-field mt-4"><label for="invitation_text_2">{{ __('builder.invitation_text_2') }}</label><textarea id="invitation_text_2" name="invitation_text_2" rows="3">{{ old('invitation_text_2', $data->invitation_text_2 ?? '') }}</textarea></div>
        <div class="builder-field mt-4"><label for="family_signature">{{ __('builder.family_signature') }}</label><input type="text" id="family_signature" name="family_signature" value="{{ old('family_signature', $data->family_signature ?? '') }}"></div>
    </fieldset>

    <fieldset class="builder-section glass-luxury rounded-2xl p-6">
        <legend class="builder-section__title font-serif text-xl font-semibold text-ink mb-4">{{ __('builder.section_music') }}</legend>
        <div class="builder-field">
            <label for="music_url">{{ __('builder.music_url') }}</label>
            <input type="url" id="music_url" name="music_url" value="{{ old('music_url', $data->music_url ?? '') }}" placeholder="https://saytingiz.uz/music/qoshiq.mp3">
            <p class="mt-2 text-xs text-ink-muted leading-relaxed">{!! __('builder.music_hint') !!}</p>
        </div>
    </fieldset>

    @if ($invitation)
        <fieldset class="builder-section glass-luxury rounded-2xl p-6">
            <legend class="builder-section__title font-serif text-xl font-semibold text-ink mb-4">{{ __('builder.section_link') }}</legend>
            <div class="builder-field">
                <label for="slug">{{ __('builder.slug') }}</label>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-ink-muted shrink-0">/i/</span>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $invitation->slug) }}" pattern="[a-z0-9\-]+">
                </div>
            </div>
        </fieldset>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
        <button type="submit" name="publish" value="0" class="btn-outline-luxury flex-1 sm:flex-none">{{ __('builder.save_draft') }}</button>
        <button type="submit" name="publish" value="1" class="btn-gold-shimmer btn-shine flex-1 sm:flex-none" data-ripple>{{ __('builder.publish') }}</button>
    </div>
</form>
