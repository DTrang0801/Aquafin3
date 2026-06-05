<x-site-layout>
    <div class="container text-center hero-box">
        <h1 class="page-title hero-title">Aquafin</h1>
        <p class="hero-team">Assia · Niels · Trang · Thien Y · Yassine</p>
    </div>

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
    @if(session('success'))
        <div class="alert-success home-alert">
            {{ session('success') }}
        </div>
    @endif

    @auth
        @if(Auth::user()->role === 'technieker' && ! empty($techniekerRainForecast))
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
    @endauth
</x-site-layout>