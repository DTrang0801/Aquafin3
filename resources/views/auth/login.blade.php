<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Login</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="login-page">

    <nav class="login-nav">
        <div class="login-nav-inner">
            <a href="{{ route('login') }}" class="login-nav-brand">Aquafin</a>
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
</html>