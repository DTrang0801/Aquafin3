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
                    @if (Auth::user()?->role_id !== \App\Models\Role::STOCKBEHEERDER)
                        <a href="{{ route('materialen') }}" class="nav-link">Materiaal bestellen</a>
                    @endif

                    @if (Auth::user()?->role_id !== \App\Models\Role::STOCKBEHEERDER && Auth::user()?->role_id !== \App\Models\Role::ADMIN)
                        <a href="{{ route('winkelmandje.index') }}" class="nav-link">Winkelmandje</a>
                    @endif

                    @if (Auth::user()?->role_id === \App\Models\Role::TECHNIEKER)
                        <a href="{{ route('bestellingen') }}" class="nav-link">Vorige bestellingen</a>
                    @endif

                    @if (Auth::user()?->role_id === \App\Models\Role::ADMIN || Auth::user()?->role_id === \App\Models\Role::STOCKBEHEERDER)
                        <a href="{{ route('weersvoorspelling') }}" class="nav-link">Neerslag</a>
                    @endif

                    @if (Auth::user()?->role_id === \App\Models\Role::ADMIN)
                        <a href="{{ route('gebruikers') }}" class="nav-link">Gebruikers</a>
                        <a href="{{ route('roles.index') }}" class="nav-link">Rollenbeheer</a>
                    @endif

                    @if (Auth::user()?->role_id === \App\Models\Role::STOCKBEHEERDER)
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
                <button class="dark-mode-toggle-mobile" id="dark-mode-toggle-mobile" aria-label="Toggle dark mode" type="button">
                    <svg class="dark-mode-icon-sun" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                    <svg class="dark-mode-icon-moon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    <span>Dark mode</span>
                </button>
            </div>
        </div>

        <div class="nav-right" id="nav-right">
            <button class="dark-mode-toggle" id="dark-mode-toggle" aria-label="Toggle dark mode" title="Toggle dark mode" type="button">
                <svg class="dark-mode-icon-sun" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                <svg class="dark-mode-icon-moon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>
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
            var desktopToggle = document.getElementById('dark-mode-toggle');
            if (desktopToggle) {
                desktopToggle.addEventListener('click', toggleDarkMode);
            }

            var mobileToggle = document.getElementById('dark-mode-toggle-mobile');
            if (mobileToggle) {
                mobileToggle.addEventListener('click', toggleDarkMode);
            }
        });
    })();
</script>

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