<!DOCTYPE html>
<html lang="uz" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#F5EBE0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="description" content="{{ $metaDescription ?? 'Nikoh to\'yi taklifnomasi' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Taklifnoma' }}</title>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/invitation.js'])

    @stack('head')
</head>
<body class="invitation-body min-h-dvh overflow-x-hidden antialiased">
    @yield('body')
    @stack('scripts')
</body>
</html>
