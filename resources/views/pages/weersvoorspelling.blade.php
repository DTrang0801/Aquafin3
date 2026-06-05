<x-site-layout>
    <div class="weather-page">
        <div class="content-grid-three">
            <div class="weather-card">
                <h1 class="card-title weather-card-title">Neerslag</h1>
                <p id="location-subtitle" class="card-subtitle">
                    @if(request()->has('lat') && request()->has('lon'))
                        Locatie: {{ $lat }}, {{ $lon }}
                    @else
                        Standaard locatie (Brussel)
                    @endif
                </p>

                @if($floodAlarmTriggered ?? false)
                    <div class="weather-alert weather-alert--danger">
                        ALARM: Overstromingsgevaar gedetecteerd! Gekoppelde voorraad is gemarkeerd als BELANGRIJK.
                        @if($isSimulated)
                            <span class="weather-alert__note">(GESIMULEERDE MODUS)</span>
                        @endif
                    </div>
                @else
                    <div class="weather-alert weather-alert--ok">
                        Status stabiel: geen verhoogd overstromingsrisico op basis van neerslagdrempels.
                    </div>
                @endif

                @if(isset($error))
                    <div class="error-alert">{{ $error }}</div>
                @else
                    <div class="stats-grid">
                        <div class="stat-box">
                            <span class="stat-label">Actuele neerslag</span>
                            <span class="stat-value value-current">
                                {{ $currentRain }} <span class="unit">mm</span>
                            </span>
                        </div>

                        <div class="stat-box highlighted">
                            <span class="stat-label">Totaal afgelopen maand</span>
                            <span class="stat-value value-history">
                                {{ $pastMonthTotal }} <span class="unit">mm</span>
                            </span>
                        </div>
                    </div>

                    <h3 class="section-title">14-daagse verwachting</h3>

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
                @endif
            </div>

            <div>
                @if(isset($fiveYearForecast) && !empty($fiveYearForecast))
                    <x-flood-forecast-card :fiveYearForecast="$fiveYearForecast" :currentYearAnalysis="$currentYearAnalysis ?? []" />
                @endif
            </div>

            <div>
                @if($canManageStock)
                    <div class="weather-card management-panel">
                <div class="simulation-panel">
                    <div>
                        <h4 class="simulation-panel__title">Systeem testen</h4>
                        <p class="simulation-panel__text">
                            Simuleer direct overstromingsgevaar om te controleren of de voorraad correct schakelt.
                        </p>
                    </div>

                    <form action="{{ route('weersvoorspelling.simulate') }}" method="POST">
                        @csrf
                        <button type="submit" class="sim-btn {{ $isSimulated ? 'active' : '' }}">
                            {{ $isSimulated ? 'Stop simulatie' : 'Start simulatie' }}
                        </button>
                    </form>
                </div>

                <h2 class="section-title management-panel__title">Beheer belangrijke items</h2>
                <p class="card-subtitle management-panel__subtitle">
                    Vink materialen aan die kritiek worden zodra er overstromingsgevaar dreigt.
                </p>

                @if(session('success'))
                    <div class="weather-alert weather-alert--ok">{{ session('success') }}</div>
                @endif

                <form action="{{ route('weersvoorspelling.store') }}" method="POST">
                    @csrf

                    @if($gekoppeldeIds)
                        <div class="important-items-section">
                            <h3 class="section-title important-items-title"> Belangrijke items</h3>
                            <div class="stock-list-container">
                                @foreach($alleMaterialen as $materials)
                                    @foreach($materials as $item)
                                        @if(in_array($item->id, $gekoppeldeIds))
                                            <label class="stock-item-row material-item material-item--important" data-name="{{ strtolower($item->naam) }}">
                                                <div class="checkbox-container">
                                                    <input
                                                        type="checkbox"
                                                        name="materiaal_ids[]"
                                                        value="{{ $item->id }}"
                                                        checked
                                                    >
                                                    <span class="stock-name">{{ $item->naam }}</span>
                                                </div>
                                                <span class="badge rain badge--small">Gekoppeld</span>
                                            </label>
                                        @endif
                                    @endforeach
                                @endforeach
                            </div>
                        </div>

                        <hr class="divider">
                    @endif

                    <h3 class="section-title">Alle materialen</h3>
                    <div class="stock-list-container">
                        @forelse($alleMaterialen as $category => $materials)
                            <div class="material-category">
                                <h4 class="category-header">{{ $category }}</h4>

                                @foreach($materials as $item)
                                    @unless(in_array($item->id, $gekoppeldeIds))
                                        <label class="stock-item-row material-item" data-name="{{ strtolower($item->naam) }}" data-category="{{ strtolower($category) }}">
                                            <div class="checkbox-container">
                                                <input
                                                    type="checkbox"
                                                    name="materiaal_ids[]"
                                                    value="{{ $item->id }}"
                                                >
                                                <span class="stock-name">{{ $item->naam }}</span>
                                            </div>
                                        </label>
                                    @endunless
                                @endforeach
                            </div>
                        @empty
                            <p class="stock-empty">Geen materialen gevonden.</p>
                        @endforelse
                    </div>

                    <button type="submit" class="submit-btn">Wijzigingen opslaan</button>
                </form>
                    </div>
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

            });
        }
    </script>
    @endunless

    <style>
        .content-grid-three {
            display: grid;
            gap: 24px;
            grid-template-columns: 1fr 1fr 1fr;
            width: 100%;
        }

        @media (max-width: 1400px) {
            .content-grid-three {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 900px) {
            .content-grid-three {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-site-layout>
