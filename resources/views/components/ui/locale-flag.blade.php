@props(['code' => 'uz'])

@php
    $uzStar = static function (float $cx, float $cy, float $s = 0.22): string {
        return sprintf(
            'M%.2f,%.2fL%.2f,%.2fL%.2f,%.2fL%.2f,%.2fL%.2f,%.2fL%.2f,%.2fL%.2f,%.2fL%.2f,%.2fL%.2f,%.2fL%.2f,%.2fZ',
            $cx, $cy - $s,
            $cx + $s * 0.31, $cy - $s * 0.12,
            $cx + $s, $cy - $s * 0.12,
            $cx + $s * 0.38, $cy + $s * 0.04,
            $cx + $s * 0.62, $cy + $s * 0.78,
            $cx, $cy + $s * 0.3,
            $cx - $s * 0.62, $cy + $s * 0.78,
            $cx - $s * 0.38, $cy + $s * 0.04,
            $cx - $s, $cy - $s * 0.12,
            $cx - $s * 0.31, $cy - $s * 0.12,
        );
    };

    $uzStars = collect([
        [4.35, 1.45], [5.35, 1.45], [6.35, 1.45],
        [3.95, 2.35], [4.75, 2.35], [5.55, 2.35], [6.35, 2.35],
        [3.55, 3.25], [4.25, 3.25], [4.95, 3.25], [5.65, 3.25], [6.35, 3.25],
    ])->map(fn (array $point) => '<path d="'.$uzStar($point[0], $point[1]).'" fill="#fff"/>')->implode('');

    $uzClipId = 'uz-clip-' . bin2hex(random_bytes(4));

    $flags = [
        'uz' => <<<SVG
<svg viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs><clipPath id="{$uzClipId}"><rect width="20" height="14" rx="2"/></clipPath></defs>
    <g clip-path="url(#{$uzClipId})">
        <rect width="20" height="4.67" fill="#0099B5"/>
        <rect y="4.67" width="20" height="4.66" fill="#fff"/>
        <rect y="9.33" width="20" height="4.67" fill="#1EB53A"/>
        <rect y="4.5" width="20" height="0.34" fill="#CE1126"/>
        <rect y="9.16" width="20" height="0.34" fill="#CE1126"/>
        <circle cx="2.65" cy="2.35" r="1.45" fill="#fff"/>
        <circle cx="3.05" cy="2.15" r="1.18" fill="#0099B5"/>
        {$uzStars}
    </g>
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
