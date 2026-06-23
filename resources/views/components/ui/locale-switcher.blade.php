@php
    use App\Support\LocaleManager;

    $current = LocaleManager::current();
    $locales = config('locales.supported', []);
    $active = $locales[$current] ?? ['label' => strtoupper($current), 'short' => strtoupper($current)];
@endphp

<div class="locale-dropdown" data-locale-dropdown>
    <button
        type="button"
        class="locale-dropdown__trigger"
        aria-expanded="false"
        aria-haspopup="listbox"
        aria-label="{{ __('nav.language') }}"
    >
        <x-ui.locale-flag :code="$current" class="locale-dropdown__flag-svg" />
        <span class="locale-dropdown__label">{{ $active['short'] }}</span>
        <svg class="locale-dropdown__chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" d="M6 9l6 6 6-6"/>
        </svg>
    </button>

    <div class="locale-dropdown__menu" role="listbox" aria-label="{{ __('nav.language') }}" hidden>
        @foreach ($locales as $code => $meta)
            <a
                href="{{ LocaleManager::switchUrl($code) }}"
                class="locale-dropdown__item {{ $current === $code ? 'is-active' : '' }}"
                hreflang="{{ $code }}"
                lang="{{ $code }}"
                role="option"
                @if ($current === $code) aria-current="true" @endif
            >
                <x-ui.locale-flag :code="$code" class="locale-dropdown__flag-svg" />
                <span class="locale-dropdown__item-label">{{ $meta['label'] }}</span>
                @if ($current === $code)
                    <svg class="locale-dropdown__check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" d="M5 13l4 4L19 7"/>
                    </svg>
                @endif
            </a>
        @endforeach
    </div>
</div>
