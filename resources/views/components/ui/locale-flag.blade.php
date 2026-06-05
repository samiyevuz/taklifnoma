@props(['code' => 'uz'])

@php
    $flags = [
        'uz' => <<<'SVG'
<svg viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <rect width="20" height="14" rx="2" fill="#1EB7A7"/>
    <rect y="4.67" width="20" height="4.66" fill="#fff"/>
    <rect y="9.33" width="20" height="4.67" fill="#1F9B4C"/>
    <rect width="6" height="14" rx="2" fill="#0099B5"/>
    <rect x="0.75" y="2.5" width="4.5" height="1.1" fill="#fff"/>
    <rect x="0.75" y="4.2" width="4.5" height="1.1" fill="#CE1126"/>
</svg>
SVG,
        'ru' => <<<'SVG'
<svg viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <rect width="20" height="14" rx="2" fill="#fff"/>
    <rect y="4.67" width="20" height="4.66" fill="#0039A6"/>
    <rect y="9.33" width="20" height="4.67" fill="#D52B1E"/>
</svg>
SVG,
        'en' => <<<'SVG'
<svg viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <rect width="20" height="14" rx="2" fill="#012169"/>
    <path d="M0 0l20 14M20 0L0 14" stroke="#fff" stroke-width="2.2"/>
    <path d="M0 0l20 14M20 0L0 14" stroke="#C8102E" stroke-width="1.1"/>
    <path d="M10 0v14M0 7h20" stroke="#fff" stroke-width="3.2"/>
    <path d="M10 0v14M0 7h20" stroke="#C8102E" stroke-width="1.8"/>
</svg>
SVG,
    ];
    $svg = $flags[$code] ?? $flags['uz'];
@endphp

<span {{ $attributes->merge(['class' => 'locale-flag']) }}>{!! $svg !!}</span>
