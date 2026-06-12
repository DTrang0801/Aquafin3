<x-site-layout>

@guest
    <div class="home-hero">
        <div class="home-logo">
            <span class="logo-fin">Aquafin</span>
        </div>
            <p class="home-slogan">Samen werken aan proper water en een gezonde leefomgeving.</p> <br>
        
            <p style="font-size: 2rem; color: #475569; font-weight: 500; letter-spacing: 0.1em; margin-top: 0.5rem; display: inline-block; padding-top: 0.5rem;">Log in om materiaal te bestellen</p>
        </div>
        @endguest

    @if(session('success'))
        <div class="alert-success home-alert">
            {{ session('success') }}
        </div>
    @endif

    @if($forecastError ?? null)
        <div class="alert-error home-alert">
            ⚠️ {{ $forecastError }}
        </div>
    @endif

    @auth
        @if(Auth::user()?->role_id === \App\Models\Role::TECHNIEKER && ! empty($techniekerRainForecast))

            <div class="go-to-order-materials">
                <a href="{{ route('materialen') }}" class="go-to-order-material__link">
                    <span>Materiaal bestellen</span>
                </a>
            </div>
            <section class="home-weather-card">
                <div class="home-weather-card__header">
                    <div>
                        <h2 class="home-weather-card__title">7-daagse neerslagverwachting</h2>
                        <p class="home-weather-card__subtitle">voorspelling voor neerslag in Brussel.</p>
                    </div>

                    <div class="home-weather-card__actions">
                        @if(! empty($homeForecastUpdatedAt))
                            <span class="home-weather-card__updated-at">
                                Laatste update: {{ $homeForecastUpdatedAt }}
                            </span>
                        @endif

                        <form method="POST" action="{{ route('home.forecast.refresh') }}" class="home-weather-card__refresh-form">
                            @csrf
                            <button type="submit" class="home-weather-card__refresh-button">
                                Data verversen
                            </button>
                        </form>
                    </div>
                </div>

                <div class="home-forecast-list">
                    @foreach($techniekerRainForecast as $forecast)
                        <div class="home-forecast-item">
                            <span class="home-forecast-item__day">{{ $forecast['day_name'] }}</span>
                            <span class="home-forecast-item__status {{ $forecast['amount'] > 0 ? 'home-forecast-item__status--rain' : 'home-forecast-item__status--dry' }}">
                                {{ $forecast['amount'] > 0 ? 'Regen' : 'Droog' }}
                            </span>
                            <span class="home-forecast-item__amount">
                                {{ number_format($forecast['amount'], 1) }} mm
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if(Auth::user()?->role_id === \App\Models\Role::TECHNIEKER)
        <div style="text-align: center; margin-top:24px;padding:16px;background:#fff3cd;border:1px solid #ffc107;border-radius:8px;">
            <p style="margin:0 0 8px 0;font-weight:600;color:#856404;">⚠️ Vergeet geen gasdetectiemateriaal!</p>
            <!-- <p style="margin:0 0 10px 0;color:#856404;font-size:14px;">Gasdetectiemeter</p> -->
            <form action="{{ route('winkelmandje.add') }}" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="materiaal_id" value="59">
                <input type="hidden" name="aantal" value="1">
                <button type="submit" style="background:#2563eb;color:#fff;border:none;padding:8px 16px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">➕ Toevoegen aan mandje</button>
            </form>
        </div>
        @endif
    @endauth

    <style>
        .home-hero {
            text-align: center;
            padding: 3rem 1rem 1rem;
        }

        .home-logo {
            font-size: 4.5rem;
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
            font-size: 1.2rem;
            color: #475569;
            font-weight: 500;
            letter-spacing: 0.1em;
            margin-top: 0.5rem;
            border-top: 2px solid #2563eb;
            display: inline-block;
            padding-top: 0.5rem;
        }

        .home-alert {
            max-width: 600px;
            margin: 1.5rem auto;
            padding: 1rem;
            border-radius: 6px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
        }

        .alert-error {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
            color: #7f1d1d;
        }
    </style>
</x-site-layout>
