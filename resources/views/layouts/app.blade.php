<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Invoice Application' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon-790x510.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/customer.css') }}">
    @stack('styles')
</head>
<body>
    <x-navbar />
    <div class="app-shell">
        <x-sidebar />
        <main class="main-content">
            <x-alert />
            {{ $slot }}
        </main>
    </div>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/flash-message.js') }}"></script>
    @stack('scripts')
</body>
</html>
