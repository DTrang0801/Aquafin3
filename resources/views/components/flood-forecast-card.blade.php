<div class="weather-card flood-forecast-card">
    <h2 class="section-title">Overstromings voorspelling (5 jaar)</h2>
    <p class="card-subtitle">Gebaseerd op neerslag per maand sinds 2004</p>

    <div class="forecast-years-grid">
        @forelse($fiveYearForecast as $yearData)
            <div class="year-forecast-card year-card--{{ $yearData['overall_risk'] }}">
                <div class="year-header">
                    <h3 class="year-title">{{ $yearData['year'] }}</h3>
                    <span class="risk-badge risk-badge--{{ $yearData['overall_risk'] }}">
                        @switch($yearData['overall_risk'])
                            @case('high')
                                🔴 Hoog risico
                            @break
                            @case('medium')
                                🟡 Gemiddeld risico
                            @break
                            @default
                                🟢 Laag risico
                        @endswitch
                    </span>
                </div>

                <div class="seasons-container">
                    @foreach($yearData['seasons'] as $season => $seasonData)
                        <div class="season-item">
                            <div class="season-name">{{ $season }}</div>
                            <div class="season-details">
                                <div class="rainfall-bar">
                                    <div 
                                        class="rainfall-fill rainfall-{{ $seasonData['risk_level'] }}"
                                        style="width: {{ min(100, ($seasonData['forecast_mm'] / $seasonData['threshold_mm']) * 100) }}%"
                                    ></div>
                                </div>
                                <div class="season-stats">
                                    <span class="forecast-mm">{{ $seasonData['forecast_mm'] }}mm</span>
                                    <span class="threshold-mm">/ {{ $seasonData['threshold_mm'] }}mm</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="year-summary">
                    <span class="at-risk-label">{{ $yearData['at_risk_seasons'] }} / 4 seizoenen met risico</span>
                </div>
            </div>
        @empty
            <div class="forecast-empty">Onvoldoende historische gegevens beschikbaar.</div>
        @endforelse
    </div>
</div>

<style scoped>
.flood-forecast-card {
    margin-top: 2rem;
}

.forecast-years-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin: 1.5rem 0;
}

.year-forecast-card {
    border: 2px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.5rem;
    background: white;
    transition: all 0.3s ease;
}

.year-card--high {
    border-color: #ef4444;
    background: #fef2f2;
}

.year-card--medium {
    border-color: #f59e0b;
    background: #fffbf0;
}

.year-card--low {
    border-color: #10b981;
    background: #f0fdf4;
}

.year-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.year-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0;
    color: #1f2937;
}

.risk-badge {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.risk-badge--high {
    background: #fee2e2;
    color: #991b1b;
}

.risk-badge--medium {
    background: #fef3c7;
    color: #92400e;
}

.risk-badge--low {
    background: #dcfce7;
    color: #166534;
}

.seasons-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin: 1rem 0;
}

.season-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.season-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.season-details {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.rainfall-bar {
    height: 1.5rem;
    background: #f3f4f6;
    border-radius: 0.375rem;
    overflow: hidden;
}

.rainfall-fill {
    height: 100%;
    transition: width 0.4s ease;
}

.rainfall-low {
    background: linear-gradient(90deg, #10b981, #34d399);
}

.rainfall-medium {
    background: linear-gradient(90deg, #f59e0b, #fbbf24);
}

.rainfall-high {
    background: linear-gradient(90deg, #ef4444, #f87171);
}

.season-stats {
    display: flex;
    gap: 0.5rem;
    font-size: 0.875rem;
    align-items: center;
}

.forecast-mm {
    font-weight: 600;
    color: #1f2937;
}

.threshold-mm {
    color: #6b7280;
}

.exceeds-badge {
    display: inline-block;
    padding: 0.125rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
    background: #fee2e2;
    color: #991b1b;
}

.year-summary {
    display: flex;
    justify-content: center;
    padding-top: 0.75rem;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    margin-top: 0.75rem;
}

.at-risk-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.divider {
    margin: 2rem 0;
    border: none;
    border-top: 1px solid #e5e7eb;
}

.current-year-section {
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.5rem;
}

.current-year-title {
    margin: 0 0 1rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

.current-season-bar-container {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.current-season-bar-wrapper {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.current-season-progress {
    height: 2rem;
    background: #e5e7eb;
    border-radius: 0.375rem;
    overflow: hidden;
}

.current-season-fill {
    height: 100%;
    transition: width 0.4s ease;
}

.current-season-low {
    background: linear-gradient(90deg, #10b981, #34d399);
}

.current-season-medium {
    background: linear-gradient(90deg, #f59e0b, #fbbf24);
}

.current-season-high {
    background: linear-gradient(90deg, #ef4444, #f87171);
}

.current-season-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.current-season-progress-text {
    color: #6b7280;
    font-weight: 400;
}

@media (max-width: 640px) {
    .forecast-years-grid {
        grid-template-columns: 1fr;
    }

    .year-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>
