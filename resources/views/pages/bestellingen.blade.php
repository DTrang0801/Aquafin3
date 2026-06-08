<x-site-layout>
    <div class="orders-page">
        <div class="orders-header">
            <div>
                <h1 class="orders-title">Mijn bestellingen</h1>
                <p class="orders-subtitle">Overzicht van jouw aangevraagde materialen.</p>
            </div>
            <a href="{{ route('materialen') }}" class="btn-new-order">
                Nieuwe bestelling
            </a>
        </div>

        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="orders-search-wrapper">
            <form method="GET" action="{{ route('bestellingen') }}" class="orders-search-form">
                <input
                    type="text"
                    name="zoekterm"
                    class="search-input"
                    placeholder="Zoek op bestelnummer, materiaal, locatie..."
                    value="{{ $zoekterm ?? '' }}"
                >
                <button type="submit" class="search-button">Zoeken</button>
                @if(($zoekterm ?? '') || ($periode ?? ''))
                    <a href="{{ route('bestellingen') }}" class="search-clear">Wis</a>
                @endif
            </form>
            <div class="orders-periodes">
                <a href="{{ route('bestellingen', array_filter(['zoekterm' => $zoekterm ?? null, 'periode' => 'vandaag'])) }}"
                    class="periode-btn {{ ($periode ?? '') === 'vandaag' ? 'periode-btn--actief' : '' }}">Vandaag</a>
                <a href="{{ route('bestellingen', array_filter(['zoekterm' => $zoekterm ?? null, 'periode' => 'week'])) }}"
                    class="periode-btn {{ ($periode ?? '') === 'week' ? 'periode-btn--actief' : '' }}">Deze week</a>
                <a href="{{ route('bestellingen', array_filter(['zoekterm' => $zoekterm ?? null, 'periode' => 'maand'])) }}"
                    class="periode-btn {{ ($periode ?? '') === 'maand' ? 'periode-btn--actief' : '' }}">Deze maand</a>
                <a href="{{ route('bestellingen', array_filter(['zoekterm' => $zoekterm ?? null, 'periode' => '3maanden'])) }}"
                    class="periode-btn {{ ($periode ?? '') === '3maanden' ? 'periode-btn--actief' : '' }}">Afgelopen 3 maanden</a>
                @if($periode ?? '')
                    <a href="{{ route('bestellingen', array_filter(['zoekterm' => $zoekterm ?? null])) }}"
                        class="periode-btn periode-btn--wis">Alle</a>
                @endif
            </div>
        </div>

        @if($bestellingen->isEmpty())
            <div class="orders-empty">
                <p>Je hebt momenteel nog geen bestellingen geplaatst.</p>
                <a href="{{ route('winkelmandje.index') }}">Naar mijn winkelmandje</a>
            </div>
        @else
            <div class="orders-list">
                @foreach($bestellingen as $bestelling)
                    <article class="order-card">
                        <header class="order-card__header">
                            <div class="order-card__meta-item order-card__meta-item--number">
                                <span class="order-card__label">Bestelnummer</span>
                                <span class="order-card__value">#{{ str_pad($bestelling->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </div>

                            <div class="order-card__meta-grid">
                                <div class="order-card__meta-item">
                                    <span class="order-card__label">Geplaatst op</span>
                                    <span class="order-card__value">{{ $bestelling->created_at->format('d-m-Y H:i') }}</span>
                                </div>
                                <div class="order-card__meta-item">
                                    <span class="order-card__label">Gewenste levering</span>
                                    <span class="order-card__value">
                                        {{ \Carbon\Carbon::parse($bestelling->gevraagde_datum)->format('d-m-Y') }}
                                    </span>
                                </div>
                                <div class="order-card__meta-item">
                                    <span class="order-card__label">Locatie</span>
                                    <span class="order-location">{{ $bestelling->locatie }}</span>
                                </div>
                            </div>
                        </header>

                        <div class="order-card__body">
                            @if($bestelling->opmerking)
                                <div class="order-note">
                                    <span class="order-card__label">Opmerking</span>
                                    <p>{{ $bestelling->opmerking }}</p>
                                </div>
                            @endif

                            <div class="order-items">
                                <div class="order-items__header">
                                    <span>Materiaal</span>
                                    <span>Aantal</span>
                                </div>

                                @foreach($bestelling->materialen as $materiaal)
                                    <div class="order-item-row">
                                        <div>
                                            <span class="order-item-name">{{ $materiaal->naam }}</span>
                                            @if($materiaal->belangrijk)
                                                <span class="checkout-critical-badge">Kritiek</span>
                                            @endif
                                        </div>
                                        <span class="order-item-quantity">
                                            {{ $materiaal->pivot->aantal }} stuks
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>

    <style>
        @media (max-width: 768px) {
            .orders-page {
                margin: 1rem auto;
                padding: 0 0.75rem;
            }

            .orders-header {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
                padding: 1.25rem;
                margin-bottom: 1rem;
            }

            .orders-title {
                font-size: 1.5rem;
                margin-bottom: 0.25rem;
            }

            .orders-subtitle {
                font-size: 0.9rem;
            }

            .btn-new-order {
                width: 100%;
                text-align: center;
                justify-content: center;
            }

            .orders-search-form {
                flex-direction: column;
                gap: 0.75rem;
            }

            .orders-search-form .search-input {
                flex: none;
                width: 100%;
            }

            .orders-search-form .search-button {
                width: 100%;
            }

            .search-clear {
                width: 100%;
                text-align: center;
            }

            .orders-periodes {
                gap: 0.4rem;
            }

            .periode-btn {
                font-size: 0.75rem;
                padding: 0.3rem 0.8rem;
                flex: 1;
                text-align: center;
            }

            .order-card {
                margin-bottom: 1rem;
            }

            .order-card__header {
                grid-template-columns: 1fr;
                gap: 0.75rem;
                padding: 1rem;
            }

            .order-card__meta-item--number {
                border-right: 0;
                border-bottom: 1px solid #bfdbfe;
                padding-right: 0;
                padding-bottom: 0.75rem;
            }

            .order-card__meta-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .order-card__body {
                padding: 1rem;
            }

            .order-note {
                padding: 0.75rem;
                margin-bottom: 0.75rem;
            }

            .order-note p {
                font-size: 0.85rem;
            }

            .order-items {
                border-radius: 8px;
            }

            .order-items__header,
            .order-item-row {
                grid-template-columns: minmax(0, 1fr) 5rem;
                gap: 0.75rem;
            }

            .order-items__header {
                padding: 0.6rem;
                font-size: 0.7rem;
            }

            .order-item-row {
                padding: 0.6rem;
            }

            .order-card__label {
                font-size: 0.65rem;
            }

            .order-card__value {
                font-size: 0.85rem;
            }

            .order-location {
                font-size: 0.8rem;
                padding: 0.2rem 0.6rem;
            }

            .order-item-name {
                font-size: 0.85rem;
            }

            .order-item-quantity {
                font-size: 0.85rem;
            }

            .orders-empty {
                padding: 2rem 1.25rem;
            }

            .orders-empty p {
                font-size: 0.9rem;
            }

            .orders-empty a {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .orders-page {
                margin: 0.5rem auto;
                padding: 0 0.5rem;
            }

            .orders-header {
                padding: 1rem;
                gap: 0.5rem;
            }

            .orders-title {
                font-size: 1.25rem;
            }

            .orders-subtitle {
                font-size: 0.85rem;
            }

            .orders-search-form {
                gap: 0.5rem;
            }

            .orders-search-form .search-input,
            .orders-search-form .search-button {
                font-size: 16px;
                height: 44px;
            }

            .orders-periodes {
                gap: 0.3rem;
                flex-wrap: wrap;
            }

            .periode-btn {
                font-size: 0.7rem;
                padding: 0.25rem 0.6rem;
            }

            .order-card {
                border-radius: 10px;
            }

            .order-card__header {
                padding: 0.75rem;
            }

            .order-card__body {
                padding: 0.75rem;
            }

            .order-note {
                padding: 0.6rem;
                font-size: 0.8rem;
            }

            .order-items__header {
                font-size: 0.65rem;
                padding: 0.5rem;
            }

            .order-item-row {
                padding: 0.5rem;
                align-items: flex-start;
            }

            .checkout-critical-badge {
                font-size: 0.65rem;
                padding: 0.1rem 0.4rem;
            }

            .container {
                padding: 1rem 0.75rem;
            }

            .page-title {
                font-size: 1.25rem;
                margin-bottom: 1rem;
            }
        }
    </style>
</x-site-layout>