<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
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
    @php
        $invitationI18n = [
            'submit' => __('invitation.rsvp_submit'),
            'submitting' => __('invitation.rsvp_submitting'),
            'success' => __('invitation.rsvp_success'),
            'error' => __('invitation.rsvp_error'),
            'networkError' => __('invitation.rsvp_network_error'),
            'musicPlay' => __('invitation.music_play'),
            'musicPause' => __('invitation.music_pause'),
            'musicError' => __('invitation.music_error'),
        ];
    @endphp
    <script>
        window.invitationI18n = @json($invitationI18n);
    </script>
</head>
<body class="invitation-body min-h-dvh overflow-x-hidden antialiased">
    @yield('body')
    @stack('scripts')
</body>
</html>
