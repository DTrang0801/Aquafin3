<x-site-layout>
    <div class="checkout-page">
        <a href="{{ route('winkelmandje.index') }}" class="checkout-back-link">
            Terug naar winkelmandje
        </a>

        <div class="checkout-card">
            <div class="checkout-header">
                <div>
                    <h1 class="checkout-title">Bestelling afronden</h1>
                    <p class="checkout-subtitle">Controleer je materialen en vul de leveringsdetails in.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="checkout-errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('winkelmandje.confirm') }}" method="POST" class="checkout-form">
                @csrf

                <div class="checkout-section">
                    <h2 class="checkout-section-title">Leveringsdetails</h2>

                    <div class="checkout-form-grid">
                        <div class="checkout-field checkout-field--full">
                            <label for="locatie" class="form-label">Leverlocatie / werf *</label>
                            <input
                                type="text"
                                name="locatie"
                                id="locatie"
                                value="{{ old('locatie') }}"
                                placeholder="Bijv. Werf Antwerpen Knooppunt Noord of Magazijn B"
                                class="form-input"
                                required
                            >
                        </div>

                        <div class="checkout-field">
                            <label for="gevraagde_datum" class="form-label">Gevraagde datum *</label>
                            <input
                                type="date"
                                name="gevraagde_datum"
                                id="gevraagde_datum"
                                value="{{ old('gevraagde_datum', date('Y-m-d')) }}"
                                min="{{ date('Y-m-d') }}"
                                class="form-input"
                                required
                            >
                        </div>

                        <div class="checkout-field">
                            <label for="gevraagde_tijd" class="form-label">Gewenst tijdstip *</label>
                            <input
                                type="time"
                                name="gevraagde_tijd"
                                id="gevraagde_tijd"
                                value="{{ old('gevraagde_tijd', '08:00') }}"
                                class="form-input"
                                required
                            >
                        </div>

                        <div class="checkout-field checkout-field--full">
                            <label for="opmerking" class="form-label">Algemene opmerking / instructies</label>
                            <textarea
                                name="opmerking"
                                id="opmerking"
                                rows="4"
                                placeholder="Voeg eventueel extra opmerkingen toe voor de stockbeheerder..."
                                class="form-input form-input--textarea"
                            >{{ old('opmerking') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="checkout-section">
                    <h2 class="checkout-section-title">Overzicht materialen</h2>
                    <table class="checkout-items-table">
                        <thead>
                            <tr>
                                <th>Materiaal</th>
                                <th class="table-right">Aantal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materialen as $item)
                                <tr>
                                    <td>
                                        <span class="checkout-item-name">{{ $item->naam }}</span>
                                        @if($item->belangrijk)
                                            <span class="checkout-critical-badge">Kritiek</span>
                                        @endif
                                    </td>
                                    <td class="table-right checkout-item-quantity">
                                        {{ $item->pivot->aantal }}x
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="checkout-actions">
                    <a href="{{ route('winkelmandje.index') }}" class="btn-secondary">
                        Annuleren
                    </a>
                    <button type="submit" class="btn-confirm-order">
                        Bestelling bevestigen
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-site-layout>