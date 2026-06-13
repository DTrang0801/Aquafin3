<x-site-layout>
    <div class="weather-page-container">
        <!-- Page Header -->
        <div class="weather-page-header">
            <div class="weather-header-content">
                <h1 class="weather-page-title">Neerslag & Overstromingsrisico</h1>
                <p id="location-subtitle" class="weather-page-subtitle">
                    @if(request()->has('lat') && request()->has('lon'))
                        Locatie: {{ $lat }}, {{ $lon }}
                    @else
                        Standaard locatie (Brussel)
                    @endif
                </p>
            </div>
        </div>

        <!-- Status Alert -->
        <div class="weather-page-alerts">
            @if($floodAlarmTriggered ?? false)
                <div class="weather-alert weather-alert--danger weather-alert--large">
                    <span class="alert-icon">!</span>
                    <div>
                        <strong>ALARM: Overstromingsgevaar gedetecteerd!</strong>
                        <p>Gekoppelde voorraad is gemarkeerd als BELANGRIJK.</p>
                        @if($isSimulated)
                            <span class="weather-alert__note">(GESIMULEERDE MODUS)</span>
                        @endif
                    </div>
                </div>
            @else
                <div class="weather-alert weather-alert--ok weather-alert--large">
                    <span class="alert-icon">✓</span>
                    <div>
                        <strong>Status stabiel</strong>
                        <p>Geen verhoogd overstromingsrisico op basis van neerslagdrempels.</p>
                    </div>
                </div>
            @endif
        </div>

        @if($canManageStock)
            <div class="weather-simulation-control">
                <div>
                    <h2 class="weather-simulation-control__title">Systeem testen (DEBUG)</h2>
                    <p class="weather-simulation-control__text">
                        Simuleer overstromingsgevaar om de neerslagstatus en gekoppelde voorraad te controleren.
                    </p>
                </div>

                <form action="{{ route('weersvoorspelling.simulate') }}" method="POST">
                    @csrf
                    <button type="submit" class="sim-btn {{ $isSimulated ? 'active' : '' }}">
                        {{ $isSimulated ? 'Stop simulatie' : 'Start simulatie' }}
                    </button>
                </form>
            </div>
        @endif

        <!-- Main Content Grid -->
        <div class="weather-page-grid">
            <!-- Add Neerslag Form (Stockbeheerder Only) -->
            @if($canManageStock)
                <div class="weather-section">
                    <x-add-neerslag-form />
                    <x-historical-neerslag-data :historicalNeerslagData="$historicalNeerslagData" />
                </div>
            @endif

            <!-- Left Column: Current Rainfall Data -->
            <div class="weather-section">
                <div class="weather-card">
                    @if(isset($error))
                        <div class="error-alert">
                            <span class="alert-icon">✕</span>
                            <div>
                                <strong>Fout bij het ophalen van weergegevens</strong>
                                <p>{{ $error }}</p>
                                <p style="font-size: 0.875rem; margin-top: 0.5rem; opacity: 0.8;">Probeer de pagina over enkele ogenblikken opnieuw in te laden.</p>
                            </div>
                        </div>
                    @else
                        <div class="section-header">
                            <h2 class="section-heading">Actuele neerslag</h2>
                        </div>

                        <div class="stats-grid">
                            <div class="stat-box">
                                <span class="stat-label">Nu</span>
                                <span class="stat-value value-current">
                                    {{ $currentRain }}<span class="unit">mm</span>
                                </span>
                            </div>

                            <div class="stat-box highlighted">
                                <span class="stat-label">Afgelopen maand</span>
                                <span class="stat-value value-history">
                                    {{ $pastMonthTotal }}<span class="unit">mm</span>
                                </span>
                            </div>
                        </div>

                        <div class="forecast-section">
                            <h3 class="subsection-heading">14-daagse verwachting</h3>

                            <div class="forecast-container">
                                @forelse($dailyRainForecast as $forecast)
                                    <div class="forecast-item">
                                        <span class="day-name">{{ $forecast['day_name'] }}</span>
                                        <div class="badge-wrapper">
                                            @if($forecast['amount'] > 0)
                                                <span class="badge rain">Regen</span>
                                                <span class="rain-amount wet">
                                                    {{ number_format($forecast['amount'], 1) }} mm
                                                </span>
                                            @else
                                                <span class="badge dry">Droog</span>
                                                <span class="rain-amount dry">0.0 mm</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="forecast-empty">Geen voorspelling beschikbaar.</p>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Middle Column: 5-Year Forecast -->
            <div class="weather-section">
                @if(isset($fiveYearForecast) && !empty($fiveYearForecast))
                    <x-flood-forecast-card :fiveYearForecast="$fiveYearForecast" :currentYearAnalysis="$currentYearAnalysis ?? []" />
                @endif
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const subtitle = document.getElementById('location-subtitle');

            if (!navigator.geolocation) {
                initSearch();
                return;
            }

            if (subtitle) {
                subtitle.textContent = 'Locatie ophalen...';
            }

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const params = new URLSearchParams({
                        lat: position.coords.latitude,
                        lon: position.coords.longitude,
                    });

                    window.location.href = `${window.location.pathname}?${params.toString()}`;
                },
                function () {
                    if (subtitle) {
                        subtitle.textContent = 'Standaard locatie (Brussel)';
                    }
                    initSearch();
                }
            );

            initSearch();
        });

        function initSearch() {
            const searchInput = document.getElementById('material-search');
            if (!searchInput) return;

            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                const items = document.querySelectorAll('.material-item');
                const categories = document.querySelectorAll('.material-category');

                items.forEach(item => {
                    const name = item.dataset.name || '';
                    const matches = name.includes(searchTerm);
                    item.classList.toggle('hidden', !matches);
                });

                categories.forEach(category => {
                    const visibleItems = category.querySelectorAll('.material-item:not(.hidden)');
                    category.style.display = visibleItems.length > 0 ? 'block' : 'none';
                });
            });
        }
    </script>

    @unless(request()->has('lat') && request()->has('lon'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const subtitle = document.getElementById('location-subtitle');

                if (!navigator.geolocation) {
                    return;
                }

                if (subtitle) {
                    subtitle.textContent = 'Locatie ophalen...';
                }

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        const params = new URLSearchParams({
                            lat: position.coords.latitude,
                            lon: position.coords.longitude,
                        });

                        window.location.href = `${window.location.pathname}?${params.toString()}`;
                    },
                    function () {
                        if (subtitle) {
                            subtitle.textContent = 'Standaard locatie (Brussel)';
                        }
                    }
                );
            });
        </script>
    @endunless
</x-site-layout>
