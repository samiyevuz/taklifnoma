@props([
    'class' => 'landing-footer__powered text-center text-xs text-royal-400',
    'linkClass' => 'font-medium text-luxury-gold transition-colors hover:text-luxury-gold/80',
])

<p {{ $attributes->merge(['class' => $class]) }}>
    Powered by
    <a
        href="https://theuzsoft.uz/ru"
        target="_blank"
        rel="noopener noreferrer"
        @class([$linkClass])
    >TheUzSoft</a>
</p>
