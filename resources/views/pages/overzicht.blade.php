<x-site-layout>
    <div class="orders-page">
        <div class="orders-header">
            <div>
                <h1 class="orders-title">Overzicht bestellingen</h1>
                <p class="orders-subtitle">Alle bestellingen van techniekers.</p>
            </div>
        </div>

        @if (session('succes'))
            <div class="alert-success">{{ session('succes') }}</div>
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
        </div>

        @if($bestellingen->isEmpty())
            <div class="orders-empty">
                <p>Nog geen bestellingen van techniekers.</p>
            </div>
        @else
            <div class="orders-list">
                @foreach($bestellingen as $bestelling)
                    <article class="order-card">
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
                                        om {{ \Carbon\Carbon::parse($bestelling->gevraagde_tijd)->format('H:i') }}
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

                        @if(!$bestelling->isGeannuleerd())
                            <footer class="order-card__footer">
                                <form method="POST" action="{{ route('bestellingen.annuleer', $bestelling->id) }}" onsubmit="return confirm('Weet je zeker dat je deze bestelling wilt annuleren?')">
                                    @csrf
                                    <button type="submit" class="btn-cancel-order">Annuleer bestelling</button>
                                </form>
                            </footer>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-site-layout>
