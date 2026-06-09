<nav class="nav">
    <div class="nav-inner">
        <div class="nav-left">
            <a href="{{ route('home') }}" class="nav-brand">Aquafin</a>
            <button class="nav-hamburger" id="nav-hamburger" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="nav-links-container" id="nav-links-container">
                @guest
                    <a href="{{ route('materialen') }}" class="nav-link">Materialen</a>
                    <a href="{{ route('winkelmandje.index') }}" class="nav-link">Mandje</a>
                    <a href="{{ route('weersvoorspelling') }}" class="nav-link">Neerslag</a>
                @endguest

                @auth
                    @if (Auth::user()->role?->name !== 'stockbeheerder')
                        <a href="{{ route('materialen') }}" class="nav-link">Materiaal bestellen</a>
                    @endif

                    @if (Auth::user()->role?->name !== 'stockbeheerder' && Auth::user()->role?->name !== 'admin')
                        <a href="{{ route('winkelmandje.index') }}" class="nav-link">Winkelmandje</a>
                    @endif

                    @if (Auth::user()->role?->name === 'technieker')
                        <a href="{{ route('bestellingen') }}" class="nav-link">Vorige bestellingen</a>
                    @endif

                    @if (Auth::user()->role?->name === 'admin' || Auth::user()->role?->name === 'stockbeheerder')
                        <a href="{{ route('weersvoorspelling') }}" class="nav-link">Neerslag</a>
                    @endif

                    @if (Auth::user()->role?->name === 'admin')
                        <a href="{{ route('gebruikers') }}" class="nav-link">Gebruikers</a>
                        <a href="{{ route('roles.index') }}" class="nav-link">Rollenbeheer</a>
                    @endif

                    @if (Auth::user()->role?->name === 'stockbeheerder')
                        <a href="{{ route('overzicht') }}" class="nav-link">Bestellingen</a>
                        <a href="{{ route('materialen.beheer') }}" class="nav-link">Beheer materiaal</a>
                        <a href="{{ route('weersvoorspelling.kritieke-items') }}" class="nav-link">Kritieke items</a>
                        <a href="{{ route('stock-dashboard') }}" class="nav-link">Meest bestelde items</a>
                    @endif

                    <div class="nav-divider"></div>
                    <span class="nav-user-info">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="nav-logout-form">
                        @csrf
                        <button class="nav-logout-mobile">Uitloggen</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="nav-link">Inloggen</a>
                @endguest
            </div>
        </div>

        <div class="nav-right" id="nav-right">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburger = document.getElementById('nav-hamburger');
        const container = document.getElementById('nav-links-container');

        if (hamburger) {
            hamburger.addEventListener('click', function() {
                container.classList.toggle('active');
                hamburger.classList.toggle('active');
            });

            document.addEventListener('click', function(event) {
                if (!hamburger.contains(event.target) && !container.contains(event.target)) {
                    container.classList.remove('active');
                    hamburger.classList.remove('active');
                }
            });

            container.querySelectorAll('a, button').forEach(element => {
                element.addEventListener('click', function() {
                    if (element.tagName !== 'BUTTON' || element.closest('.nav-logout-form')) {
                        container.classList.remove('active');
                        hamburger.classList.remove('active');
                    }
                });
            });
        }
    });
</script>