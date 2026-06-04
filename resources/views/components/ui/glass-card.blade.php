@props([
    'title' => null,
    'subtitle' => null,
    'border' => 'gold',
])

@php
    $borderClass = match ($border) {
        'purple' => 'glass-border-purple',
        'emerald' => 'border border-luxury-emerald/30',
        default => 'glass-border-gold',
    };
@endphp

<article
    {{ $attributes->merge(['class' => "glass-card {$borderClass} animate-scale-in"]) }}
>
    @if ($title || $subtitle)
        <header class="mb-fluid-sm">
            @if ($title)
                <h3 class="font-serif text-fluid-subtitle text-royal-900 dark:text-cream-100 text-balance">
                    {{ $title }}
                </h3>
            @endif
            @if ($subtitle)
                <p class="mt-1 text-fluid-body text-royal-600 dark:text-royal-500">
                    {{ $subtitle }}
                </p>
            @endif
        </header>
    @endif

    <div class="text-fluid-body text-royal-700 dark:text-cream-200/90">
        {{ $slot }}
    </div>

    @isset($footer)
        <footer class="mt-fluid-md pt-fluid-sm border-t border-cream-300/50 dark:border-royal-600/50">
            {{ $footer }}
        </footer>
    @endisset
</article>
