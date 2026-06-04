<nav class="nav">
    <div class="nav-inner">
        <div class="nav-left">
            <a href="{{ route('home') }}" class="nav-brand">Aquafin</a>
            <a href="{{ route('materialen') }}" class="nav-link">Materialen</a>
            @auth
                @if (Auth::user()->role !== 'stockbeheerder')
                    <a href="{{ route('winkelmandje') }}" class="nav-link">Mandje</a>
                @endif
            @endauth
            <a href="{{ route('bestellingen') }}" class="nav-link">Bestellingen</a>
            @auth
                @if (Auth::user()->role !== 'stockbeheerder')
                    <a href="{{ route('weersvoorspelling') }}" class="nav-link">Weer</a>
                @endif
            @endauth
            @auth
                @if (Auth::user()->role === 'stockbeheerder' || Auth::user()->role === 'admin')
                    <a href="{{ route('favorieten') }}" class="nav-link">Favorieten</a>
                @endif
            @endauth

            @auth
                @if (Auth::user()->role === 'admin')
                    <a href="{{ route('gebruikers') }}" class="nav-link">Gebruikers</a>
                @endif
            @endauth

            @auth
                @if (Auth::user()->role === 'stockbeheerder')
                    <a href="{{ route('materialen.create') }}" class="nav-link">Nieuw Materiaal</a>
                @endif
            @endauth
        </div>

        <div class="nav-right">
            @auth
                <span class="nav-user">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline-form">
                    @csrf
                    <button class="btn btn-logout">Uitloggen</button>
                </form>
            @endauth
            @guest
                <a href="{{ route('login') }}" class="btn btn-login">Inloggen</a>
            @endguest
        </div>
    </div>
</nav>