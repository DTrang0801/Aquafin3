<div class="weather-card flood-forecast-card">
    <div class="section-header">
        <h2 class="section-heading">Overstromingsvoorspelling (5 jaar)</h2>
        <p class="section-description">Gebaseerd op neerslag per maand sinds 2004</p>
    </div>

    <div class="forecast-years-grid">
        @forelse($fiveYearForecast as $yearData)
            <div class="year-forecast-card year-card--{{ $yearData['overall_risk'] }}">
                <div class="year-header">
                    <h3 class="year-title">{{ $yearData['year'] }}</h3>
                    <span class="risk-badge risk-badge--{{ $yearData['overall_risk'] }}">
                        @switch($yearData['overall_risk'])
                            @case('high')
                                Hoog risico
                            @break
                            @case('medium')
                                Gemiddeld risico
                            @break
                            @default
                                Laag risico
                        @endswitch
                    </span>
                </div>

                <div class="seasons-container">
                    @foreach($yearData['seasons'] as $season => $seasonData)
                        <div class="season-item">
                            <div class="season-header">
                                <span class="season-name">{{ $season }}</span>
                                <span class="season-stats">
                                    <span class="forecast-mm">{{ $seasonData['forecast_mm'] }}mm</span>
                                    <span class="threshold-mm">/ {{ $seasonData['threshold_mm'] }}mm</span>
                                </span>
                            </div>
                            <div class="rainfall-bar">
                                <div 
                                    class="rainfall-fill rainfall-{{ $seasonData['risk_level'] }}"
                                    style="width: {{ min(100, ($seasonData['forecast_mm'] / $seasonData['threshold_mm']) * 100) }}%"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="year-summary">
                    <span class="at-risk-label">
                        {{ $yearData['at_risk_seasons'] }} / 4 seizoenen met risico
                    </span>
                </div>
            </div>
        @empty
            <div class="forecast-empty">Onvoldoende historische gegevens beschikbaar.</div>
        @endforelse
    </div>
</div>
