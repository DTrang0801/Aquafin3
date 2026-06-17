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
            @php
                $riskLevel = $currentRiskLevel ?? \App\Enums\FloodRiskLevel::Low;
                $riskValue = $riskLevel instanceof \App\Enums\FloodRiskLevel ? $riskLevel->value : (string) $riskLevel;
                $riskLabel = $riskLevel instanceof \App\Enums\FloodRiskLevel ? $riskLevel->label() : ucfirst($riskValue);
            @endphp

            @if($riskValue === 'high')
                <div class="weather-alert weather-alert--danger weather-alert--large">
                    <span class="alert-icon">!</span>
                    <div>
                        <strong>ALARM: Ernstig overstromingsgevaar gedetecteerd!</strong>
                        <p>Neerslag overschrijdt 120% van de seizoensdrempel. Alle kritieke materialen zijn gemarkeerd.</p>
                        @if($isSimulated)
                            <span class="weather-alert__note">(GESIMULEERDE MODUS)</span>
                        @endif
                    </div>
                    <span class="risk-badge risk-badge--high risk-badge--large">Hoog risico</span>
                </div>
            @elseif($riskValue === 'medium')
                <div class="weather-alert weather-alert--warning weather-alert--large">
                    <span class="alert-icon">⚠</span>
                    <div>
                        <strong>WAARSCHUWING: Verhoogd overstromingsrisico</strong>
                        <p>Neerslag bereikt de seizoensdrempel. Materialen met drempel Gemiddeld of lager zijn gemarkeerd.</p>
                        @if($isSimulated)
                            <span class="weather-alert__note">(GESIMULEERDE MODUS)</span>
                        @endif
                    </div>
                    <span class="risk-badge risk-badge--medium risk-badge--large">Gemiddeld risico</span>
                </div>
            @else
                <div class="weather-alert weather-alert--ok weather-alert--large">
                    <span class="alert-icon">✓</span>
                    <div>
                        <strong>Status stabiel</strong>
                        <p>Geen verhoogd overstromingsrisico op basis van neerslagdrempels.</p>
                    </div>
                    <span class="risk-badge risk-badge--low risk-badge--large">Laag risico</span>
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

                <div class="simulation-buttons">
                    <form action="{{ route('weersvoorspelling.simulate') }}" method="POST" class="sim-form">
                        @csrf
                        <input type="hidden" name="level" value="medium">
                        <button type="submit" class="sim-btn {{ $isSimulated && session('simulate_level') === 'medium' ? 'active' : '' }}">
                            Simuleer Gemiddeld risico
                        </button>
                    </form>
                    <form action="{{ route('weersvoorspelling.simulate') }}" method="POST" class="sim-form">
                        @csrf
                        <input type="hidden" name="level" value="high">
                        <button type="submit" class="sim-btn {{ $isSimulated && session('simulate_level') === 'high' ? 'active' : '' }}">
                            Simuleer Hoog risico
                        </button>
                    </form>
                    <form action="{{ route('weersvoorspelling.simulate') }}" method="POST" class="sim-form">
                        @csrf
                        <input type="hidden" name="level" value="none">
                        <button type="submit" class="sim-btn {{ !$isSimulated ? 'active' : '' }}">
                            Stop simulatie
                        </button>
                    </form>
                </div>
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

                            <div class="stat-box">
                                <span class="stat-label">Afgelopen maand</span>
                                <span class="stat-value value-history">
                                    {{ $pastMonthTotal }}<span class="unit">mm</span>
                                </span>
                            </div>

                            <div class="stat-box highlighted">
                                <span class="stat-label">Afgelopen 3 maanden</span>
                                <span class="stat-value value-history">
                                    {{ $pastThreeMonthsTotal }}<span class="unit">mm</span>
                                </span>
                            </div>

                            <div class="stat-box">
                                <span class="stat-label">Risico percentage</span>
                                <span class="stat-value value-risk" style="color: {{ $riskPercentage >= 120 ? '#dc2626' : ($riskPercentage >= 100 ? '#d97706' : '#16a34a') }};">
                                    {{ number_format($riskPercentage, 1) }}<span class="unit">%</span>
                                </span>
                                <span class="risk-sublabel">
                                    @if($riskPercentage < 100)
                                        {{ number_format(100 - $riskPercentage, 1) }}% van medium risico
                                    @elseif($riskPercentage < 120)
                                        {{ number_format(120 - $riskPercentage, 1) }}% van hoog risico
                                    @else
                                        Boven hoog risico drempel
                                    @endif
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
