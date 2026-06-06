@props([
    'invitation',
    'compact' => false,
    'showPlatformLink' => true,
])

@if ($invitation->isPublished())
    @php
        $primaryUrl = $invitation->primaryShareUrl();
        $platformUrl = $invitation->publicUrl();
        $customUrl = $invitation->customDomainUrl();
        $showBoth = $showPlatformLink && $customUrl && $customUrl !== $platformUrl;
    @endphp

    <div class="share-bar {{ $compact ? 'share-bar--compact' : '' }}" data-copy-link-root>
        <div class="share-bar__intro">
            <div class="share-bar__icon" aria-hidden="true">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" d="M13.19 8.688a4.5 4.5 0 015.656 0l.547.547a4.5 4.5 0 010 6.364l-1.06 1.06a4.5 4.5 0 01-6.364 0"/>
                    <path stroke-linecap="round" d="M10.81 15.312a4.5 4.5 0 01-5.656 0l-.547-.547a4.5 4.5 0 010-6.364l1.06-1.06a4.5 4.5 0 016.364 0"/>
                </svg>
            </div>
            <div>
                <p class="share-bar__label">{{ __('share.title') }}</p>
                <p class="share-bar__desc">{{ __('share.desc') }}</p>
            </div>
        </div>

        <div class="share-bar__group {{ $showBoth ? '' : 'share-bar__group--single' }}">
            <div class="share-bar__field">
                <label class="share-bar__field-label">
                    {{ $customUrl ? __('share.primary_custom') : __('share.primary') }}
                </label>
                <div class="share-bar__row">
                    <input
                        type="text"
                        class="share-bar__input"
                        value="{{ $primaryUrl }}"
                        readonly
                        data-copy-target
                        aria-label="{{ __('share.primary') }}"
                    >
                    <button
                        type="button"
                        class="share-bar__btn share-bar__btn--copy"
                        data-copy-btn
                        data-copy-default="{{ __('share.copy') }}"
                        data-copy-success="{{ __('share.copied') }}"
                    >
                        {{ __('share.copy') }}
                    </button>
                    <a href="{{ $primaryUrl }}" target="_blank" rel="noopener" class="share-bar__btn share-bar__btn--open">
                        {{ __('share.open') }}
                    </a>
                </div>
            </div>

            @if ($showBoth)
                <div class="share-bar__field">
                    <label class="share-bar__field-label">{{ __('share.platform') }}</label>
                    <div class="share-bar__row">
                        <input
                            type="text"
                            class="share-bar__input"
                            value="{{ $platformUrl }}"
                            readonly
                            data-copy-target
                            aria-label="{{ __('share.platform') }}"
                        >
                        <button
                            type="button"
                            class="share-bar__btn share-bar__btn--copy"
                            data-copy-btn
                            data-copy-default="{{ __('share.copy') }}"
                            data-copy-success="{{ __('share.copied') }}"
                        >
                            {{ __('share.copy') }}
                        </button>
                        <a href="{{ $platformUrl }}" target="_blank" rel="noopener" class="share-bar__btn share-bar__btn--open">
                            {{ __('share.open') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif
