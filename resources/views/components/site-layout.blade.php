<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Aquafin</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body>

<nav class="mb-4 bg-blue-300 flex items-center px-4 py-2">

    <div class="flex gap-6">
        <a class="hover:font-bold text-3xl" href="/">AQUAFIN</a>
        <a class="ml-8 hover:font-bold text-xl" href="/materiaalbestellen">Materiaal bestellen</a>
        <a class="ml-8 hover:font-bold text-xl" href="/neerslagvoorspelling">Neerslagvoorspelling</a>
    </div>

    <div class="ml-auto flex gap-6">

        @guest
            <a class="hover:font-bold" href="/register">Register</a>
            <a class="hover:font-bold" href="/login">Login</a>
        @endguest

        @auth
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="hover:font-bold">
                {{ __('Logout') }}
            </button>
        </form>

        @endauth

    </div>

</nav>


<main>
    {{ $slot }}
</main>


<footer class="mt-4 mb-4 bg-blue-200 flex gap-6">

    <a class="ml-8 mr-4 hover:font-bold" href="/contact">Assia</a>
    <a class="hover:font-bold">Niels</a>
    <a class="hover:font-bold">Thien Trang</a>
    <a class="hover:font-bold">Thien Y</a>
    <a class="hover:font-bold">Yassine</a>

</footer>

</body>
</html>