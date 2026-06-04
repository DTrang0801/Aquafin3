<x-site-layout>
    <div style="align-items: center; display: flex; flex-direction: column; gap: 24px; margin-top: 32px;">
        <div class="weather-card">
            <h1 class="card-title" style="color: white;">Neerslag</h1>

            @if($floodAlarmTriggered)
                <div style="background-color: rgba(239, 68, 68, 0.15); color: #f87171; padding: 12px; border-radius: 8px; border: 1px solid rgba(239, 68, 68, 0.3); margin-bottom: 20px; text-align: center; font-size: 14px; font-weight: bold;">
                        ALARM: Overstromingsgevaar gedetecteerd! Gekoppelde voorraad is gemarkeerd als BELANGRIJK.
                    @if($isSimulated)
                        <span style="display:block; font-size: 11px; font-weight: normal; color: #fca5a5; margin-top: 2px;">(GESIMULEERDE MODUS)</span>
                    @endif
                </div>
            @else
                <div style="background-color: rgba(16, 185, 129, 0.1); color: #34d399; padding: 12px; border-radius: 8px; border: 1px solid rgba(16, 185, 129, 0.2); margin-bottom: 20px; text-align: center; font-size: 14px; font-weight: 500;">
                       Status stabiel: Geen verhoogd overstromingsrisico op basis van neerslag thresholds.
                </div>
            @endif
            @if(isset($error))
                <div class="error-alert">
                    {{ $error }}
                </div>
            @else
                <!-- Overview Numbers Grid -->
                <div class="stats-grid">
                    <div class="stat-box">
                        <span class="stat-label">Actuele Neerslag</span>
                        <span class="stat-value value-current">
                            {{ $currentRain }} <span class="unit">mm</span>
                        </span>
                    </div>
                    
                    <div class="stat-box highlighted">
                        <span class="stat-label">Totaal Afgelopen Maand</span>
                        <span class="stat-value value-history">
                            {{ $pastMonthTotal }} <span class="unit">mm</span>
                        </span>
                    </div>
                </div>

                <h3 class="section-title">14-Daagse Verwachting</h3>
                
                <!-- 14 Day Grid Block -->
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
                        <p style="grid-column: span 2; text-align: center; color: #64748b; font-size: 14px;">
                            Geen voorspelling beschikbaar.
                        </p>
                    @endforelse
                </div>
            @endif
        </div>
        <div class="weather-card management-panel" style="margin-top: 24px;">

            <hr style="border: 0; border-top: 1px solid #334155; margin: 20px 0;">

            <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(245, 158, 11, 0.05); border: 1px dashed rgba(245, 158, 11, 0.3); padding: 14px; border-radius: 8px;">
                <div>
                    <h4 style="font-size: 13px; font-weight: bold; color: #f59e0b; margin-bottom: 2px;">🧪 Systeem Testen</h4>
                    <p style="font-size: 11px; color: #94a3b8; max-width: 340px; margin: 0;">Simuleer direct overstromingsgevaar om te controleren of de voorraad correct schakelt.</p>
                </div>
                
                <form action="{{ route('weersvoorspelling.simulate') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="sim-btn {{ $isSimulated ? 'active' : '' }}">
                        {{ $isSimulated ? 'Stop Simulatie' : 'Start Simulatie' }}
                    </button>
                </form>
            </div>

            <style>
                .sim-btn {
                    background-color: #d97706; /* amber-600 */
                    color: white;
                    font-size: 12px;
                    font-weight: 700;
                    padding: 8px 16px;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    transition: background 0.2s ease;
                    white-space: nowrap;
                }
                .sim-btn:hover { background-color: #b45309; }
                .sim-btn.active {
                    background-color: #dc2626; /* red-600 */
                }
                .sim-btn.active:hover { background-color: #b91c1c; }
            </style>

            <h2 class="section-title" style="font-size: 16px; margin-bottom: 8px; color: #f1f5f9;">
                Beheer Belangrijke Items (Stockbeheerder)
            </h2>
            <p class="card-subtitle" style="text-align: left; margin-bottom: 16px;">
                Vink de materialen aan die kritiek worden (`belangrijk = true`) zodra er overstromingsgevaar dreigt.
            </p>

            @if(session('success'))
                <div style="background-color: rgba(16, 185, 129, 0.15); color: #34d399; padding: 12px; border-radius: 8px; border: 1px solid rgba(16, 185, 129, 0.3); margin-bottom: 16px; font-size: 14px; font-weight: 500;">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('weersvoorspelling.store') }}" method="POST">
                @csrf
                <div class="stock-list-container">
                    @forelse($alleMaterialen as $item)
                        <label class="stock-item-row">
                            <div class="checkbox-container">
                                <input type="checkbox" name="materiaal_ids[]" value="{{ $item->id }}"
                                    {{ in_array($item->id, $gekoppeldeIds) ? 'checked' : '' }}>
                                <span class="stock-name" style="color: #f1f5f9;">{{ $item->naam }}</span>
                            </div>
                            
                            @if(in_array($item->id, $gekoppeldeIds))
                                <span class="badge rain" style="font-size: 9px; padding: 1px 5px;">Gekoppeld</span>
                            @endif
                        </label>
                    @empty
                        <p style="color: #64748b; font-size: 13px; font-style: italic; padding: 8px 0;">
                            Geen materialen gevonden in de `materialen` database.
                        </p>
                    @endforelse
                </div>

                <button type="submit" class="submit-btn">
                    Wijzigingen Opslaan
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Check if coordinates are already present in the current URL address bar
            const urlParams = new URLSearchParams(window.location.search);
            const hasLat = urlParams.has('lat');
            const hasLon = urlParams.has('lon');

            // If coordinates are NOT in the URL yet, ask the browser for them
            if (!hasLat || !hasLon) {
                if (navigator.geolocation) {
                    
                    // Display a temporary status notice to the user
                    document.getElementById('location-subtitle').innerText = "Locatie ophalen...";

                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;

                            // Reload the page passing the exact user coordinates as query strings
                            window.location.href = window.location.pathname + `?lat=${latitude}&lon=${longitude}`;
                        },
                        function(error) {
                            // If user blocks permission, it falls back to the default coordinates seamlessly
                            console.warn("Locatiebepaling geweigerd of mislukt. Standaard locatie wordt getoond.");
                            document.getElementById('location-subtitle').innerText = "Standaard locatie (Brussel)";
                        }
                    );
                }
            }
        });
    </script>
</x-site-layout>