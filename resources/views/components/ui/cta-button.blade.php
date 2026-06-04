@props([
    'href' => null,
    'variant' => 'gold',
    'type' => 'button',
])

@php
    $variantClass = $variant === 'purple' ? 'btn-premium--purple' : '';
    $classes = trim("btn-premium {$variantClass} " . ($attributes->get('class') ?? ''));
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        role="button"
        {{ $attributes->merge(['class' => $classes, 'data-ripple' => true]) }}
    >
        <span class="relative z-10 flex items-center justify-center gap-2">
            {{ $slot }}
        </span>
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes, 'data-ripple' => true]) }}
    >
        <span class="relative z-10 flex items-center justify-center gap-2">
            {{ $slot }}
        </span>
    </button>
@endif
