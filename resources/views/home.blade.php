<x-site-layout>
    <div class="container text-center hero-box">
        <h1 class="page-title hero-title">Aquafin</h1>
        <p class="hero-team">Assia · Niels · Trang · Thien Y · Yassine</p>
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