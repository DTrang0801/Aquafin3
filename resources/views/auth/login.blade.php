<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Login</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="login-page">

    <nav class="login-nav">
        <div class="login-nav-inner">
            <a href="{{ route('login') }}" class="login-nav-brand">Aquafin</a>
            <button class="dark-mode-toggle" id="dark-mode-toggle" aria-label="Toggle dark mode" title="Toggle dark mode" type="button">
                <svg class="dark-mode-icon-sun" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                <svg class="dark-mode-icon-moon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>
        </div>
    </nav>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="text-center">
                <h1 class="login-title">Login</h1>
                <p class="login-subtitle">Login om verder te gaan</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-input">
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Wachtwoord</label>
                    <input id="password" type="password" name="password" required class="form-input">
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Inloggen</button>
            </form>
        </div>
    </div>

</body>
<script>
    (function() {
        function toggleDarkMode(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            var html = document.documentElement;
            var isDark = html.classList.toggle('dark');
            localStorage.setItem('darkMode', isDark ? 'true' : 'false');
        }

        document.addEventListener('DOMContentLoaded', function() {
            var toggle = document.getElementById('dark-mode-toggle');
            if (toggle) {
                toggle.addEventListener('click', toggleDarkMode);
            }
        });
    })();
</script>
</html>