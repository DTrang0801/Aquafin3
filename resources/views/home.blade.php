<x-site-layout>
    <div class="home-container">
        <div class="home-brand">
            <div class="home-logo">
                <span class="logo-aqua">Aqua</span><span class="logo-fin">fin</span>
            </div>
            <p class="home-slogan">Waterbeheer in goede banen</p>
        </div>
    </div>

    <style>
        .home-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 160px);
            padding: 2rem;
        }

        .home-brand {
            text-align: center;
        }

        .home-logo {
            font-size: 5.5rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            line-height: 1.1;
        }

        .logo-aqua {
            color: #2563eb;
        }

        .logo-fin {
            color: #1e3a5f;
        }

        .home-slogan {
            font-size: 1.4rem;
            color: #475569;
            font-weight: 500;
            letter-spacing: 0.1em;
            margin-top: 0.75rem;
            border-top: 2px solid #2563eb;
            display: inline-block;
            padding-top: 0.75rem;
        }
    </style>
</x-site-layout>
