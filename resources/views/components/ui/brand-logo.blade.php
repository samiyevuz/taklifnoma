@props([
    'showText' => true,
    'size' => 'md',
])

@php
    $gid = 'brand-' . substr(md5((string) $attributes->get('data-logo-id', 'taklifnoma')), 0, 8);
@endphp

<span {{ $attributes->class(['brand-logo', "brand-logo--{$size}"]) }}>
    <span class="brand-logo__mark" aria-hidden="true">
        <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" role="img">
            <defs>
                <linearGradient id="{{ $gid }}-gold" x1="6" y1="6" x2="34" y2="34" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#F0D78C"/>
                    <stop offset="45%" stop-color="#C9A227"/>
                    <stop offset="100%" stop-color="#8B6914"/>
                </linearGradient>
                <linearGradient id="{{ $gid }}-seal" x1="14" y1="14" x2="26" y2="26" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#34D399"/>
                    <stop offset="100%" stop-color="#0D6B5C"/>
                </linearGradient>
                <linearGradient id="{{ $gid }}-card" x1="12" y1="15" x2="28" y2="27" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.95"/>
                    <stop offset="100%" stop-color="#F5E6C8" stop-opacity="0.75"/>
                </linearGradient>
            </defs>

            <rect x="3" y="3" width="34" height="34" rx="10" fill="url(#{{ $gid }}-gold)" fill-opacity="0.16"/>
            <rect x="3" y="3" width="34" height="34" rx="10" stroke="url(#{{ $gid }}-gold)" stroke-opacity="0.35" stroke-width="1"/>

            <path
                d="M9 15.5h22a1.5 1.5 0 011.5 1.5V28a2.5 2.5 0 01-2.5 2.5H10A2.5 2.5 0 017.5 28V17a1.5 1.5 0 011.5-1.5z"
                class="brand-logo__envelope"
            />
            <path
                d="M9 16.5l11 7.2L31 16.5"
                class="brand-logo__flap"
                stroke-linecap="round"
                stroke-linejoin="round"
            />

            <rect x="13.5" y="18" width="13" height="9.5" rx="1.5" fill="url(#{{ $gid }}-card)"/>
            <path d="M15.5 21.5h9M15.5 24h6" class="brand-logo__lines" stroke-linecap="round"/>

            <circle cx="20" cy="22.8" r="4" fill="url(#{{ $gid }}-seal)"/>
            <path d="M20 21v3.6M18.2 22.8h3.6" stroke="#FFFBF5" stroke-width="1.1" stroke-linecap="round"/>
        </svg>
    </span>

    @if ($showText)
        <span class="brand-logo__text">
            <span class="brand-logo__title">Taklifnoma</span>
            <span class="brand-logo__sub">Premium</span>
        </span>
    @endif
</span>
