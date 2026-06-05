@php
    use App\Support\LocaleManager;

    $current = LocaleManager::current();
    $locales = config('locales.supported', []);
@endphp

<div class="locale-switcher" role="group" aria-label="{{ __('nav.language') }}">
    @foreach ($locales as $code => $meta)
        <a
            href="{{ route('locale.switch', $code) }}"
            class="locale-switcher__btn {{ $current === $code ? 'is-active' : '' }}"
            hreflang="{{ $code }}"
            lang="{{ $code }}"
            @if ($current === $code) aria-current="true" @endif
            title="{{ $meta['label'] }}"
        >
            <span class="locale-switcher__flag" aria-hidden="true">{{ $meta['flag'] }}</span>
            <span class="locale-switcher__code">{{ $meta['short'] }}</span>
        </a>
    @endforeach
</div>
