<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aquafin</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    @include('layouts.app_navigation')

    <main>
        {{ $slot }}
    </main>

    <footer style="text-align:center; padding:1rem; color:#6b7280; font-size:0.875rem; border-top:1px solid #e5e7eb; margin-top:2rem;">
        Assia · Niels · Thien Trang · Thien Y · Yassine
    </footer>

</body>
</html>