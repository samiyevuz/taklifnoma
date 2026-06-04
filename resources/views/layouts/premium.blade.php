<!DOCTYPE html>
<html lang="uz" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0F0E17">
    <meta name="description" content="{{ $metaDescription ?? 'Raqamli taklifnoma platformasi — O\'zbekiston' }}">

    <title>{{ $title ?? config('app.name', 'Taklifnoma') }}</title>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="bg-mesh-premium min-h-dvh text-royal-900 dark:text-cream-100">
    <a
        href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-4 focus:left-4 focus:px-4 focus:py-2 focus:rounded-lg focus:bg-luxury-gold focus:text-royal-950"
    >
        Asosiy kontentga o'tish
    </a>

    @yield('body')

    @stack('scripts')
</body>
</html>
