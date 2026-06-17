<x-site-layout>
    <div class="orders-page">
        <div class="orders-header">
            <div>
                <h1 class="orders-title">Overzicht bestellingen</h1>
                <p class="orders-subtitle">Alle bestellingen van techniekers.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if($todayOrderCount > 0)
            <div class="orders-today-info">
                <p><strong>{{ $todayOrderCount }}</strong> {{ $todayOrderCount === 1 ? 'bestelling' : 'bestellingen' }} vandaag geplaatst</p>
            </div>
        @else
            <div class="orders-today-info orders-today-info--empty">
                <p>Geen nieuwe bestellingen vandaag</p>
            </div>
        @endif

        <div class="orders-search-wrapper">
            <form method="GET" action="{{ route('overzicht') }}" class="orders-search-form">
                <input type="text" name="zoekterm" class="search-input"
                    placeholder="Zoek op bestelnummer, technieker, materiaal, locatie..."
                    value="{{ $zoekterm ?? '' }}">
                <button type="submit" class="search-button">Zoeken</button>
                @if(($zoekterm ?? '') || ($periode ?? ''))
                    <a href="{{ route('overzicht') }}" class="search-clear">Wis</a>
                @endif
            </form>
            <div class="orders-filter-row">
                <div class="orders-periodes">
                    <a href="{{ route('overzicht', array_filter(['zoekterm' => $zoekterm ?? null, 'periode' => 'vandaag'])) }}"
                        class="periode-btn {{ ($periode ?? '') === 'vandaag' ? 'periode-btn--actief' : '' }}">Vandaag</a>
                    <a href="{{ route('overzicht', array_filter(['zoekterm' => $zoekterm ?? null, 'periode' => 'week'])) }}"
                        class="periode-btn {{ ($periode ?? '') === 'week' ? 'periode-btn--actief' : '' }}">Deze week</a>
                    <a href="{{ route('overzicht', array_filter(['zoekterm' => $zoekterm ?? null, 'periode' => 'maand'])) }}"
                        class="periode-btn {{ ($periode ?? '') === 'maand' ? 'periode-btn--actief' : '' }}">Deze maand</a>
                    <a href="{{ route('overzicht', array_filter(['zoekterm' => $zoekterm ?? null, 'periode' => '3maanden'])) }}"
                        class="periode-btn {{ ($periode ?? '') === '3maanden' ? 'periode-btn--actief' : '' }}">Afgelopen 3 maanden</a>
                    @if($periode ?? '')
                        <a href="{{ route('overzicht', array_filter(['zoekterm' => $zoekterm ?? null])) }}"
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
                <p>Nog geen bestellingen van techniekers.</p>
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
                                        <span class="order-card__label">Technieker</span>
                                        <span class="order-card__value">{{ $bestelling->gebruiker->name }}</span>
                                    </div>
                                    <div class="order-card__meta-item">
                                        <span class="order-card__label">Geplaatst op</span>
                                        <span class="order-card__value">{{ $bestelling->created_at->format('d-m-Y H:i') }}</span>
                                    </div>
                                    <div class="order-card__meta-item">
                                        <span class="order-card__label">Gewenste levering</span>
                                        <span class="order-card__value">
                                            {{ \Carbon\Carbon::parse($bestelling->gevraagde_datum)->format('d-m-Y') }}
                                            @if($bestelling->gevraagde_tijd)
                                                om {{ \Carbon\Carbon::parse($bestelling->gevraagde_tijd)->format('H:i') }}
                                            @endif
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

                            @if(!$bestelling->isGeannuleerd())
                                <footer class="order-card__footer">
                                    @can('update', $bestelling)
                                        <a href="{{ route('bestellingen.edit', $bestelling->id) }}" class="btn-action btn-action-edit">Bewerk bestelling</a>
                                    @endcan
                                    <form method="POST" action="{{ route('bestellingen.annuleer', $bestelling->id) }}" onsubmit="return confirm('Weet je zeker dat je deze bestelling wilt annuleren?')">
                                        @csrf
                                        <button type="submit" class="btn-action btn-action-delete">Annuleer bestelling</button>
                                    </form>
                                </footer>
                            @endif
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
        }
    </style>
</x-site-layout>
