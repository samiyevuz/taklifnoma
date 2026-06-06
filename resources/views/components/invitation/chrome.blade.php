@props(['invitation'])

<div class="invitation-chrome">
    <div class="invitation-chrome__locale">
        <x-ui.locale-switcher />
    </div>

    @if ($invitation->allowsMusic())
        <button
            type="button"
            class="inv-music"
            id="inv-music"
            aria-label="{{ __('invitation.music_play') }}"
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
            preload="auto"
        ></audio>
    @endif
</div>
