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
            <div class="orders-filter-row">
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
                @php
                    $geannuleerdCount = $bestellingen->filter->isGeannuleerd()->count();
                @endphp
                @if($geannuleerdCount > 0)
                    <button type="button" class="btn-filter-cancelled" id="toggleCancelled">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <span id="toggleText">Toon geannuleerde</span>
                        <span class="filter-count">{{ $geannuleerdCount }}</span>
                    </button>
                @endif
            </div>
        </div>

        @if($bestellingen->isEmpty())
            <div class="orders-empty">
                <p>Je hebt momenteel nog geen bestellingen geplaatst.</p>
                <a href="{{ route('winkelmandje.index') }}">Naar mijn winkelmandje</a>
            </div>
        @else
            <div class="orders-list" id="ordersList">
                @foreach($bestellingen as $bestelling)
                    <article class="order-card order-card--collapsible {{ $bestelling->isGeannuleerd() ? 'is-geannuleerd' : '' }}">
                        <button class="order-card__toggle" data-order-id="{{ $bestelling->id }}" type="button">
                            <header class="order-card__header">
                                <div class="order-card__meta-item order-card__meta-item--number">
                                    <div class="order-card__number-wrapper">
                                        <div>
                                            <span class="order-card__label">Bestelnummer</span>
                                            <span class="order-card__value">#{{ str_pad($bestelling->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                        @if($bestelling->isGeannuleerd())
                                            <span class="badge-cancelled">Geannuleerd</span>
                                        @elseif($bestelling->is_edited)
                                            <span class="badge-edited">Bewerkt</span>
                                        @endif
                                    </div>
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

                                <div class="order-card__expand-indicator">
                                    <svg class="order-card__expand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                            </header>
                        </button>

                        <div class="order-card__body order-card__body--hidden" id="order-body-{{ $bestelling->id }}">
                            @if(!$bestelling->isGeannuleerd())
                                <div class="order-card__actions">
                                    @if($bestelling->canStillBeEdited())
                                        <a href="{{ route('bestellingen.edit', $bestelling->id) }}" class="btn-cancel-order" style="background: var(--primary, #2563eb); text-decoration: none;">Bewerk bestelling</a>
                                    @endif
                                    <form method="POST" action="{{ route('bestellingen.reorder', $bestelling->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-cancel-order" style="background: var(--primary, #2563eb);">Herbestellen</button>
                                    </form>
                                    @if($bestelling->kanNogGeannuleerdWorden())
                                        <form method="POST" action="{{ route('bestellingen.annuleer', $bestelling->id) }}" onsubmit="return confirm('Weet je zeker dat je deze bestelling wilt annuleren?')">
                                            @csrf
                                            <button type="submit" class="btn-cancel-order">Annuleer bestelling</button>
                                        </form>
                                    @endif
                                </div>
                            @endif

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
                                                <span class="checkout-critical-badge checkout-critical-badge--{{ $materiaal->belangrijk->value }}">{{ $materiaal->belangrijk->label() }}</span>
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

        <script>
            document.querySelectorAll('.order-card__toggle').forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.dataset.orderId;
                    const bodyElement = document.getElementById('order-body-' + orderId);
                    const card = this.closest('.order-card--collapsible');
                    const icon = this.querySelector('.order-card__expand-icon');

                    bodyElement.classList.toggle('order-card__body--hidden');
                    card.classList.toggle('order-card--expanded');
                    icon.style.transform = icon.style.transform === 'rotate(180deg)' ? 'rotate(0deg)' : 'rotate(180deg)';
                });
            });

            const toggleBtn = document.getElementById('toggleCancelled');
            if (toggleBtn) {
                const ordersList = document.getElementById('ordersList');
                const toggleText = document.getElementById('toggleText');
                let cancelledVisible = false;

                toggleBtn.addEventListener('click', function() {
                    cancelledVisible = !cancelledVisible;
                    ordersList.classList.toggle('show-cancelled', cancelledVisible);
                    toggleText.textContent = cancelledVisible ? 'Verberg geannuleerde' : 'Toon geannuleerde';
                });
            }
        </script>
    </div>

    <style>
        .orders-filter-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .btn-filter-cancelled {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-medium);
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-filter-cancelled:hover {
            border-color: var(--danger);
            color: var(--danger);
            background: var(--danger-light);
        }

        .filter-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.25rem;
            height: 1.25rem;
            padding: 0 0.35rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: white;
            background: var(--danger);
            border-radius: 999px;
        }

        .order-card--collapsible {
            overflow: visible;
        }

        .orders-list .is-geannuleerd {
            display: none;
        }

        .orders-list.show-cancelled .is-geannuleerd {
            display: block;
        }

        .orders-list.show-cancelled .order-card--collapsible:not(.is-geannuleerd) {
            display: none;
        }

        .order-card__toggle {
            background: none;
            border: none;
            cursor: pointer;
            display: block;
            padding: 0;
            text-align: left;
            width: 100%;
        }

        .order-card__toggle:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
            border-radius: 16px;
        }

        .order-card__header {
            display: grid;
            grid-template-columns: minmax(150px, 0.55fr) minmax(0, 1.45fr) 2rem;
            align-items: center;
            gap: 1rem;
        }

        .order-card__expand-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
        }

        .order-card__expand-icon {
            width: 1.25rem;
            height: 1.25rem;
            color: var(--text-medium);
            transition: transform 0.3s ease;
        }

        .order-card__body {
            transition: max-height 0.3s ease, opacity 0.3s ease, padding 0.3s ease;
            max-height: 1000px;
            opacity: 1;
            overflow: hidden;
        }

        .order-card__body--hidden {
            max-height: 0;
            opacity: 0;
            padding: 0;
            visibility: hidden;
        }

        @media (max-width: 768px) {
            .orders-filter-row {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-filter-cancelled {
                justify-content: center;
            }

            .order-card__header {
                grid-template-columns: 1fr 2rem;
                gap: 0.75rem;
            }

            .order-card__meta-item--number {
                grid-column: 1 / -1;
                border-right: 0;
                border-bottom: 1px solid #bfdbfe;
                padding-right: 0;
                padding-bottom: 0.75rem;
            }

            .order-card__meta-grid {
                grid-column: 1 / -1;
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .order-card__expand-indicator {
                grid-column: 2 / 3;
                grid-row: 1;
            }

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
                padding: 1rem;
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