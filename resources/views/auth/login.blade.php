<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | Invoice Application</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon-790x510.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
</head>
<body class="auth-page">
    <main class="auth-wrap">
        <section class="auth-card">
            <div class="auth-heading">
                <div class="auth-logo-frame">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Ebluesoft logo">
                </div>
                <h1>Welcome back</h1>
                <p>Sign in to continue to your account</p>
            </div>

            <x-alert />

            @if ($errors->any())
                <div class="alert alert-error" data-flash-message>
                    <span>Please fix the highlighted fields.</span>
                    <button class="alert-close" type="button" data-flash-close aria-label="Close message">X</button>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="auth-form">
                @csrf

                <label class="auth-field">
                    <span>Email address</span>
                    <div class="auth-input">
                        <i class="fa-regular fa-user" aria-hidden="true"></i>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" autocomplete="email" autofocus required>
                    </div>
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </label>

                <label class="auth-field">
                    <span>Password</span>
                    <div class="auth-input">
                        <i class="fa-solid fa-lock" aria-hidden="true"></i>
                        <input id="login-password" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                        <button class="auth-input-action" type="button" data-password-toggle aria-label="Show password" aria-controls="login-password">
                            <i class="fa-regular fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </label>

                <div class="auth-options">
                    <label class="remember-check">
                        <input type="checkbox" name="remember" checked>
                        <span>Remember me</span>
                    </label>
                </div>

                <button class="btn btn-primary auth-submit" type="submit">Login</button>
            </form>
        </section>
    </main>

    <script src="{{ asset('assets/js/flash-message.js') }}"></script>
    <script src="{{ asset('assets/js/auth-login.js') }}"></script>
</body>
</html>
