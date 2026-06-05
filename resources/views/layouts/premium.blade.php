<!DOCTYPE html>
<html lang="uz" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#FAF6F0" id="meta-theme-color">
    <script>
        (function () {
            var t = sessionStorage.getItem('theme');
            var d = t === 'dark' || (t === null && matchMedia('(prefers-color-scheme: dark)').matches);
            if (d) {
                document.documentElement.classList.add('dark');
                var m = document.getElementById('meta-theme-color');
                if (m) m.setAttribute('content', '#0B0B0F');
            }
        })();
    </script>
    <meta name="description" content="{{ $metaDescription ?? 'Raqamli taklifnoma platformasi — O\'zbekiston' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Taklifnoma') }}</title>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="min-h-dvh antialiased">
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
