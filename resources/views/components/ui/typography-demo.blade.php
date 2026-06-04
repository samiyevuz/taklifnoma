@props([
    'hero' => "To'yingizga raqamli taklifnoma yarating",
    'subtitle' => "Hashamatli dizayn, silliq animatsiyalar va har qanday ekranda mukammal ko'rinish",
    'body' => "O'zbekistondagi eng zamonaviy onlayn taklifnoma platformasi. Mehmonlaringizga unutilmas taassurot qoldiring — bir necha daqiqada professional taklifnoma tayyorlang.",
])

<section {{ $attributes->merge(['class' => 'space-y-fluid-md animate-slide-up']) }}>
    <p class="text-xs uppercase tracking-widest text-luxury-gold font-semibold">
        Premium UI Kit
    </p>

    <h1 class="text-fluid-hero text-balance text-royal-900 dark:text-cream-50">
        {{ $hero }}
    </h1>

    <p class="text-fluid-subtitle text-royal-600 dark:text-cream-200/80 text-pretty max-w-prose">
        {{ $subtitle }}
    </p>

    <p class="text-fluid-body text-royal-500 dark:text-royal-500 max-w-prose leading-relaxed">
        {{ $body }}
    </p>
</section>
