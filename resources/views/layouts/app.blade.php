<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Invoice Application' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon-790x510.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/customer.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/icon-buttons.css') }}">
</head>
<body>
    <x-navbar />
    <div class="sidebar-overlay" data-sidebar-overlay></div>
    <div class="delete-confirm-modal" id="deleteConfirmModal" aria-hidden="true">
        <div class="delete-confirm-card" role="dialog" aria-modal="true" aria-labelledby="deleteConfirmTitle">
            <h2 id="deleteConfirmTitle">Confirm Delete</h2>
            <p data-delete-confirm-message>Are you sure you want to delete this record?</p>
            <div class="delete-confirm-actions">
                <button class="btn delete-confirm-cancel" type="button" data-delete-cancel>Cancel</button>
                <button class="btn delete-confirm-submit" type="button" data-delete-confirm>Delete</button>
            </div>
        </div>
    </div>
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
    <script src="{{ asset('assets/js/responsive-menu.js') }}"></script>
</body>
</html>
