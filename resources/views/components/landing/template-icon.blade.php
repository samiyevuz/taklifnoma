@props(['slug'])

@php
    $class = $attributes->get('class', 'template-card__icon');
@endphp

<svg {{ $attributes->merge(['class' => $class, 'viewBox' => '0 0 48 48', 'fill' => 'none', 'aria-hidden' => 'true']) }}>
    @switch($slug)
        @case('nikoh')
            <circle cx="18" cy="24" r="9" stroke="currentColor" stroke-width="1.75"/>
            <circle cx="30" cy="24" r="9" stroke="currentColor" stroke-width="1.75"/>
            <path d="M24 14v4M21 11l3 3 3-3" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            @break
        @case('qiz')
            <path d="M24 8c-4 6-10 9-10 16a10 10 0 1020 0c0-7-6-10-10-16z" stroke="currentColor" stroke-width="1.75"/>
            <path d="M20 30c1.5 2 3 3 4 3s2.5-1 4-3" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            @break
        @case('sunnat')
            <circle cx="24" cy="16" r="6" stroke="currentColor" stroke-width="1.75"/>
            <path d="M12 38c2-8 7-12 12-12s10 4 12 12" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            <path d="M30 12l2-3M18 12l-2-3" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            @break
        @case('beshik')
            <path d="M10 30h28v6H10z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
            <path d="M14 30V22c0-4 3.5-7 10-7s10 3 10 7v8" stroke="currentColor" stroke-width="1.75"/>
            <path d="M18 18c2-3 5-4 6-4s4 1 6 4" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            @break
        @case('yubiley')
            <path d="M16 34V18l8-6 8 6v16" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
            <path d="M20 34V24h8v10" stroke="currentColor" stroke-width="1.75"/>
            <path d="M22 14h4" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            @break
        @case('nahor')
            <ellipse cx="24" cy="28" rx="14" ry="6" stroke="currentColor" stroke-width="1.75"/>
            <path d="M14 28c2-6 6-10 10-10s8 4 10 10" stroke="currentColor" stroke-width="1.75"/>
            <path d="M20 20l2-4M28 20l-2-4" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            @break
        @case('fotiha')
            <path d="M16 26c0-4.5 3.5-8 8-8s8 3.5 8 8" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            <path d="M14 26h20M24 18v-4M20 12h8" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            <circle cx="24" cy="30" r="2" fill="currentColor"/>
            @break
        @case('birthday')
            <path d="M14 32h20v4H14z" stroke="currentColor" stroke-width="1.75"/>
            <path d="M18 32V22h12v10" stroke="currentColor" stroke-width="1.75"/>
            <path d="M20 22c0-3 1.5-5 4-5s4 2 4 5" stroke="currentColor" stroke-width="1.75"/>
            <path d="M22 14v2M26 14v2" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            @break
        @case('muchal')
            <rect x="12" y="14" width="24" height="22" rx="3" stroke="currentColor" stroke-width="1.75"/>
            <path d="M12 22h24M18 10v4M30 10v4" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            <path d="M20 30h8" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            @break
        @case('iftorlik')
            <path d="M24 10c-8 0-12 6-12 11a12 12 0 1024 0c0-5-4-11-12-11z" stroke="currentColor" stroke-width="1.75"/>
            <path d="M20 18c2 2 4 3 4 6" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            <circle cx="32" cy="16" r="1.5" fill="currentColor"/>
            @break
        @case('aqiyqa')
            <path d="M24 10l3 8h8l-6.5 5 2.5 8-7-5-7 5 2.5-8L13 18h8l3-8z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
            @break
        @case('bitiruv')
            <path d="M10 20l14-8 14 8-14 8-14-8z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
            <path d="M24 28v10M18 34h12" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            <path d="M30 18v6l4 2" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            @break
        @default
            <circle cx="24" cy="24" r="10" stroke="currentColor" stroke-width="1.75"/>
    @endswitch
</svg>
