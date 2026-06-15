<x-site-layout>
    <div class="weather-page-container critical-items-page">
        <div class="weather-page-header">
            <div class="weather-header-content">
                <h1 class="weather-page-title">Beheer Kritieke Items</h1>
                <p class="weather-page-subtitle">
                    Kies welke materialen automatisch belangrijk worden wanneer er overstromingsgevaar dreigt,
                    en stel per materiaal in vanaf welk risiconiveau het gemarkeerd wordt.
                </p>
            </div>
        </div>

        <div class="stock-management-dashboard">
            <div class="weather-card management-panel">
                <div class="management-topbar">
                    <div class="section-header">
                        <h2 class="section-heading">Kritieke materialen</h2>
                        <p class="section-description">
                            Gekoppelde materialen worden gemarkeerd bij verhoogd overstromingsrisico.
                            Het risiconiveau bepaalt wanneer een materiaal actief wordt gemarkeerd.
                        </p>
                    </div>
                </div>

                <div class="risk-level-legend">
                    <span class="risk-legend-item">
                        <span class="risk-badge risk-badge--medium">Gemiddeld</span>
                        Materiaal wordt gemarkeerd bij ≥ 100% seizoensdrempel
                    </span>
                    <span class="risk-legend-item">
                        <span class="risk-badge risk-badge--high">Hoog</span>
                        Materiaal wordt gemarkeerd bij ≥ 120% seizoensdrempel
                    </span>
                </div>

                @if(session('success'))
                    <div class="weather-alert weather-alert--ok">
                        <span class="alert-icon">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('weersvoorspelling.store') }}" method="POST" class="management-form">
                    @csrf

                    <div class="material-picker">
                        <section class="material-picker-panel material-picker-panel--active">
                            <h3 class="material-picker-title">Gekoppeld ({{ count($gekoppeldeIds) }})</h3>

                            <div class="stock-list-container stock-list-container--compact">
                                @forelse($alleMaterialen->flatten(1)->filter(fn ($item) => in_array($item->id, $gekoppeldeIds)) as $item)
                                    @php
                                        $currentLevel = $gekoppeldeRiskLevels[$item->id] ?? \App\Enums\FloodRiskLevel::Medium;
                                        $currentLevelValue = $currentLevel instanceof \App\Enums\FloodRiskLevel
                                            ? $currentLevel->value
                                            : (string) $currentLevel;
                                    @endphp
                                    <label class="stock-item-row material-item material-item--important" data-name="{{ strtolower($item->naam) }}">
                                        <div class="checkbox-container">
                                            <input
                                                type="checkbox"
                                                name="materiaal_ids[]"
                                                value="{{ $item->id }}"
                                                checked
                                            >
                                            <span>
                                                <span class="stock-name">{{ $item->naam }}</span>
                                                <span class="stock-meta">
                                                    {{ $item->subcategorie?->categorie?->naam ?? 'Overig' }}
                                                    @if($item->subcategorie?->naam)
                                                        · {{ $item->subcategorie->naam }}
                                                    @endif
                                                </span>
                                            </span>
                                        </div>
                                        <div class="risk-level-control">
                                            <select
                                                name="risk_levels[{{ $item->id }}]"
                                                class="risk-level-select risk-level-select--{{ $currentLevelValue }}"
                                                onchange="this.className = 'risk-level-select risk-level-select--' + this.value"
                                            >
                                                @foreach($riskLevelOptions as $option)
                                                    <option
                                                        value="{{ $option->value }}"
                                                        {{ $currentLevelValue === $option->value ? 'selected' : '' }}
                                                    >{{ $option->label() }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </label>
                                @empty
                                    <p class="stock-empty">Nog geen kritieke materialen gekoppeld.</p>
                                @endforelse
                            </div>
                        </section>

                        <section class="material-picker-panel">
                            <div class="material-picker-heading-row">
                                <h3 class="material-picker-title">Beschikbaar</h3>
                                <input
                                    type="search"
                                    id="material-search"
                                    class="material-search-input"
                                    placeholder="Zoek materiaal..."
                                >
                            </div>

                            <div class="stock-list-container stock-list-container--compact stock-list-container--available">
                                @forelse($alleMaterialen->flatten(1)->filter(fn ($item) => ! in_array($item->id, $gekoppeldeIds)) as $item)
                                    <label class="stock-item-row material-item" data-name="{{ strtolower($item->naam) }}">
                                        <div class="checkbox-container">
                                            <input
                                                type="checkbox"
                                                name="materiaal_ids[]"
                                                value="{{ $item->id }}"
                                            >
                                            <span>
                                                <span class="stock-name">{{ $item->naam }}</span>
                                                <span class="stock-meta">
                                                    {{ $item->subcategorie?->categorie?->naam ?? 'Overig' }}
                                                    @if($item->subcategorie?->naam)
                                                        · {{ $item->subcategorie->naam }}
                                                    @endif
                                                </span>
                                            </span>
                                        </div>
                                        {{-- Default to medium when a new item is checked --}}
                                        <input type="hidden" name="risk_levels[{{ $item->id }}]" value="medium">
                                    </label>
                                @empty
                                    <p class="stock-empty">Geen beschikbare materialen gevonden.</p>
                                @endforelse
                            </div>
                        </section>
                    </div>

                    <button type="submit" class="submit-btn submit-btn--full">
                        Wijzigingen opslaan
                    </button>
                </form>
            </div>

            <!-- <x-add-material-form /> -->
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('material-search');
            if (!searchInput) {
                return;
            }

            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                const items = document.querySelectorAll('.material-item');

                items.forEach(item => {
                    const name = item.dataset.name || '';
                    item.classList.toggle('hidden', !name.includes(searchTerm));
                });
            });
        });
    </script>
</x-site-layout>
