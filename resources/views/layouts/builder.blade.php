<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-seo.head :seo="$seo ?? null" />
    @php
        $builderI18n = array_merge([
            'continue' => __('builder.continue'),
            'save' => __('builder.save'),
            'checkout_url_pending' => __('builder.checkout_url_pending'),
        ], __('builder.js'));
        $builderI18n['review'] = __('builder.review');
        $builderI18n['placeholders'] = array_merge(
            $builderI18n['placeholders'] ?? [],
            __('builder.js.placeholders')
        );
    @endphp
    <script>
        window.builderI18n = @json($builderI18n);
    </script>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/builder.js'])
    @stack('head')
</head>
<body class="min-h-dvh antialiased builder-body">
    <a href="#builder-main" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-4 focus:left-4 focus:px-4 focus:py-2 focus:rounded-lg focus:bg-luxury-gold focus:text-royal-950">
        {{ __('nav.skip_to_content') }}
    </a>
    @yield('body')
    @stack('scripts')
</body>
</html>
