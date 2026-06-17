<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aquafin</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.1/dist/cdn.min.js"></script>
</head>
<body>

    @include('layouts.app_navigation')

    <main>
        {{ $slot }}
    </main>

</body>
</html>