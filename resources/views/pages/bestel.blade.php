<x-site-layout>
    <div class="checkout-page">
        <a href="{{ route('winkelmandje.index') }}" class="checkout-back-link">
            Terug naar winkelmandje
        </a>

        <div class="checkout-card">
            <div class="checkout-header">
                <div>
                    <h1 class="checkout-title">Bestelling afronden</h1>
                    <p class="checkout-subtitle">Controleer je materiaal en vul de leveringsdetails in.</p>
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

                    @if ($user->role === 'technieker' && $user->province)
                        <div class="checkout-field checkout-field--full">
                            <div style="background: #f0f9ff; border-left: 4px solid #0ea5e9; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
                                <p style="margin: 0; font-size: 14px; color: #333;">
                                    <strong>Standaard leverlocatie ({{ $user->province }}):</strong>
                                </p>
                                <p style="margin: 4px 0 0 0; font-size: 14px; color: #666;">
                                    {{ $user->getDepotLocation() }}
                                </p>
                            </div>

                            <label style="display: flex; align-items: center; margin-bottom: 16px; cursor: pointer;">
                                <input
                                    type="checkbox"
                                    name="use_custom_location"
                                    id="use_custom_location"
                                    value="1"
                                    onchange="toggleCustomLocation()"
                                    {{ old('use_custom_location') ? 'checked' : '' }}
                                    style="margin-right: 8px; cursor: pointer;"
                                >
                                <span>Gebruik aangepaste locatie (uitzondering)</span>
                            </label>

                            <div id="custom-location-field" style="display: {{ old('use_custom_location') ? 'block' : 'none' }};">
                                <label for="locatie" class="form-label">Aangepaste leverlocatie</label>
                                <input
                                    type="text"
                                    name="locatie"
                                    id="locatie"
                                    value="{{ old('locatie') }}"
                                    placeholder="Bijv. Werf Antwerpen Knooppunt Noord of Magazijn B"
                                    class="form-input"
                                >
                            </div>
                        </div>
                    @else
                        <div class="checkout-field checkout-field--full">
                            <label for="locatie" class="form-label">Leverlocatie *</label>
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
                    @endif

                    <div class="checkout-form-grid">
                        <div class="checkout-field">
                            <label for="gevraagde_datum" class="form-label">Gevraagde datum *</label>
                            <input
                                type="date"
                                name="gevraagde_datum"
                                id="gevraagde_datum"
                                value="{{ old('gevraagde_datum', now()->next('Monday')->format('Y-m-d')) }}"
                                min="{{ date('Y-m-d') }}"
                                class="form-input"
                                required
                            >
                        </div>

                        <div class="checkout-field checkout-field--full">
                            <label for="opmerking" class="form-label">Opmerking</label>
                            <textarea
                                name="opmerking"
                                id="opmerking"
                                rows="4"
                                placeholder="Voeg eventueel extra opmerkingen toe..."
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

    <script>
        function toggleCustomLocation() {
            const checkbox = document.getElementById('use_custom_location');
            const customField = document.getElementById('custom-location-field');
            const locationInput = document.getElementById('locatie');
            
            if (checkbox.checked) {
                customField.style.display = 'block';
                locationInput.required = true;
            } else {
                customField.style.display = 'none';
                locationInput.required = false;
                locationInput.value = '';
            }
        }
    </script>

    <style>
        @media (max-width: 768px) {
            .checkout-page {
                margin: 1rem auto;
                padding: 0 0.75rem;
            }

            .checkout-back-link {
                font-size: 0.875rem;
                margin-bottom: 0.75rem;
            }

            .checkout-card {
                border-radius: 12px;
            }

            .checkout-header {
                padding: 1.25rem 1rem;
            }

            .checkout-title {
                font-size: 1.5rem;
            }

            .checkout-subtitle {
                font-size: 0.9rem;
            }

            .checkout-errors {
                margin: 1rem 1rem 0;
                padding: 0.875rem 1rem;
            }

            .checkout-form {
                padding: 1.5rem 1rem;
                gap: 1.5rem;
            }

            .checkout-section {
                padding: 1.25rem;
                border-radius: 10px;
            }

            .checkout-section-title {
                font-size: 1rem;
                margin-bottom: 1rem;
            }

            .checkout-form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .checkout-page .form-input {
                min-height: 2.5rem;
                font-size: 16px;
            }

            .checkout-items-table {
                font-size: 13px;
            }

            .checkout-items-table th,
            .checkout-items-table td {
                padding: 0.75rem;
            }

            .checkout-item-quantity {
                width: 6rem;
            }

            .checkout-actions {
                flex-direction: column-reverse;
                gap: 0.5rem;
            }

            .btn-secondary,
            .btn-confirm-order {
                width: 100%;
                min-height: 2.5rem;
                font-size: 0.875rem;
            }
        }

        @media (max-width: 480px) {
            .checkout-page {
                margin: 0.5rem auto;
                padding: 0 0.5rem;
            }

            .checkout-header {
                padding: 1rem;
            }

            .checkout-title {
                font-size: 1.25rem;
            }

            .checkout-form {
                padding: 1rem;
                gap: 1.25rem;
            }

            .checkout-section {
                padding: 1rem;
            }

            .checkout-section-title {
                font-size: 0.95rem;
                margin-bottom: 0.875rem;
            }

            .checkout-page .form-label {
                font-size: 0.85rem;
            }

            .checkout-page .form-input {
                min-height: 44px;
                padding: 0.75rem;
            }

            .checkout-items-table {
                font-size: 12px;
            }

            .checkout-items-table th,
            .checkout-items-table td {
                padding: 0.6rem;
            }

            .checkout-item-name {
                display: block;
                word-break: break-word;
            }

            .checkout-critical-badge {
                font-size: 0.7rem;
                margin-left: 0.25rem;
            }
        }
    </style>
</x-site-layout>