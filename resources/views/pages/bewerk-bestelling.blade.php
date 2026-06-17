<x-site-layout>
    <div class="checkout-page">
        <a href="{{ route('bestellingen') }}" class="checkout-back-link">
            Terug naar bestellingen
        </a>

        <div class="checkout-card">
            <div class="checkout-header">
                <div>
                    <h1 class="checkout-title">Bestelling bewerken</h1>
                    <p class="checkout-subtitle">Je kunt deze bestelling nog tot 1 dag na plaatsing bewerken.</p>
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

            <form action="{{ route('bestellingen.update', $bestelling->id) }}" method="POST" class="checkout-form">
                @csrf
                @method('PUT')

                <div class="checkout-section">
                    <h2 class="checkout-section-title">Leveringsdetails</h2>

                    @if (Auth::user()->role_id === \App\Models\Role::TECHNIEKER && Auth::user()->province)
                        <div class="checkout-field checkout-field--full">
                            <div style="background: #f0f9ff; border-left: 4px solid #0ea5e9; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
                                <p style="margin: 0; font-size: 14px; color: #333;">
                                    <strong>Standaard leverlocatie ({{ Auth::user()->province }}):</strong>
                                </p>
                                <p style="margin: 4px 0 0 0; font-size: 14px; color: #666;">
                                    {{ Auth::user()->getDepotLocation() }}
                                </p>
                            </div>

                            <label style="display: flex; align-items: center; margin-bottom: 16px; cursor: pointer;">
                                <input
                                    type="checkbox"
                                    name="use_custom_location"
                                    id="use_custom_location"
                                    value="1"
                                    onchange="toggleCustomLocation()"
                                    {{ $bestelling->custom_location_used ? 'checked' : '' }}
                                    style="margin-right: 8px; cursor: pointer;"
                                >
                                <span>Gebruik aangepaste locatie (uitzondering)</span>
                            </label>

                            <div id="custom-location-field" style="display: {{ $bestelling->custom_location_used ? 'block' : 'none' }};">
                                <label for="locatie" class="form-label">Aangepaste leverlocatie</label>
                                <input
                                    type="text"
                                    name="locatie"
                                    id="locatie"
                                    value="{{ $bestelling->custom_location_used ? $bestelling->locatie : '' }}"
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
                                value="{{ $bestelling->locatie }}"
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
                                value="{{ $bestelling->gevraagde_datum }}"
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
                            >{{ $bestelling->opmerking }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="checkout-section">
                    <h2 class="checkout-section-title">Materialen bewerken</h2>
                    
                    <div id="materials-list" style="margin-bottom: 1.5rem;">
                        @forelse($bestelling->materialen as $item)
                            <div class="material-edit-item" data-material-id="{{ $item->id }}">
                                <div class="material-edit-row">
                                    <div class="material-info">
                                        <span class="material-name">{{ $item->naam }}</span>
                                        @if($item->belangrijk)
                                            <span class="checkout-critical-badge checkout-critical-badge--{{ $item->belangrijk->value }}">{{ $item->belangrijk->label() }}</span>
                                        @endif
                                    </div>
                                    <div class="material-controls">
                                        <input 
                                            type="number" 
                                            name="materials[{{ $item->id }}]" 
                                            value="{{ $item->pivot->aantal }}" 
                                            min="1" 
                                            max="10000"
                                            class="material-quantity-input"
                                            onchange="updateOverview()"
                                        >
                                        <button 
                                            type="button" 
                                            class="btn-remove-material" 
                                            onclick="removeMaterial({{ $item->id }})"
                                            title="Verwijder materiaal"
                                        >✕</button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p style="color: #666; font-style: italic; margin-bottom: 1rem;">Geen materialen in deze bestelling.</p>
                        @endforelse
                    </div>

                    <div id="removed-materials" style="display: none;"></div>

                    <button 
                        type="button" 
                        id="add-material-btn" 
                        class="btn-add-material"
                        onclick="openMaterialSelector()"
                    >+ Materiaal toevoegen</button>

                    <!-- Hidden modal for material selection -->
                    <div id="material-selector-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; padding: 1rem;">
                        <div style="background: white; border-radius: 12px; max-width: 600px; max-height: 80vh; margin: auto; padding: 1.5rem; overflow-y: auto;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                                <h3 style="margin: 0; font-size: 1.25rem; color: #1f2937;">Materiaal toevoegen</h3>
                                <button 
                                    type="button" 
                                    onclick="closeMaterialSelector()" 
                                    style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #666;"
                                >✕</button>
                            </div>

                            <input 
                                type="text" 
                                id="material-search" 
                                class="form-input"
                                placeholder="Zoek materiaal..." 
                                onkeyup="searchMaterials()"
                                style="margin-bottom: 1rem;"
                            >

                            <div id="material-list" style="max-height: 50vh; overflow-y: auto;"></div>
                        </div>
                    </div>
                </div>

                <div class="checkout-section">
                    <h2 class="checkout-section-title">Huige materialen (overzicht)</h2>
                    <table class="checkout-items-table" id="materials-overview">
                        <thead>
                            <tr>
                                <th>Materiaal</th>
                                <th class="table-right">Aantal</th>
                            </tr>
                        </thead>
                        <tbody id="materials-overview-body">
                            @foreach($bestelling->materialen as $item)
                                <tr data-material-id="{{ $item->id }}">
                                    <td>
                                        <span class="checkout-item-name">{{ $item->naam }}</span>
                                        @if($item->belangrijk)
                                            <span class="checkout-critical-badge checkout-critical-badge--{{ $item->belangrijk->value }}">{{ $item->belangrijk->label() }}</span>
                                        @endif
                                    </td>
                                    <td class="table-right checkout-item-quantity">
                                        <span id="qty-{{ $item->id }}">{{ $item->pivot->aantal }}</span>x
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="checkout-actions">
                    <a href="{{ route('bestellingen') }}" class="btn-secondary">
                        Annuleren
                    </a>
                    <button type="submit" class="btn-confirm-order">
                        Wijzigingen opslaan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let availableMaterials = [];
        let removedMaterials = new Set();

        // Fetch available materials on page load
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                const res = await fetch('{{ route('materialen.json') }}');
                const data = await res.json();
                availableMaterials = data;
            } catch (err) {
                console.error('Error fetching materials:', err);
            }
        });

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

        function openMaterialSelector() {
            document.getElementById('material-selector-modal').style.display = 'flex';
            renderMaterialList();
        }

        function closeMaterialSelector() {
            document.getElementById('material-selector-modal').style.display = 'none';
            document.getElementById('material-search').value = '';
        }

        function searchMaterials() {
            renderMaterialList();
        }

        function renderMaterialList() {
            const searchTerm = document.getElementById('material-search').value.toLowerCase();
            const list = document.getElementById('material-list');
            
            const currentMaterialIds = new Set(
                Array.from(document.querySelectorAll('.material-edit-item')).map(el => 
                    parseInt(el.dataset.materialId)
                )
            );

            const filtered = availableMaterials.filter(m => {
                const matchesSearch = m.naam.toLowerCase().includes(searchTerm);
                const notAlreadyAdded = !currentMaterialIds.has(m.id);
                const notRemoved = !removedMaterials.has(m.id);
                return matchesSearch && notAlreadyAdded && notRemoved;
            });

            const riskLabels = { medium: 'Gemiddeld', high: 'Hoog', low: 'Laag' };
            list.innerHTML = filtered.map(material => `
                <div style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb; cursor: pointer;" 
                     onclick="addMaterial(${material.id}, '${material.naam.replace(/'/g, "\\'")}', ${material.belangrijk ? `'${material.belangrijk}'` : 'null'})">
                    <div style="font-weight: 600; color: #1f2937;">${material.naam}${material.belangrijk ? ` <span class="checkout-critical-badge checkout-critical-badge--${material.belangrijk}">${riskLabels[material.belangrijk] || material.belangrijk}</span>` : ''}</div>
                    <div style="font-size: 0.875rem; color: #666;">${material.subcategorie?.naam || ''}</div>
                </div>
            `).join('');

            if (filtered.length === 0) {
                list.innerHTML = '<div style="padding: 1rem; text-align: center; color: #999;">Geen materialen gevonden</div>';
            }
        }

        function addMaterial(materialId, materialName, criticalLevel) {
            const isCritical = criticalLevel && criticalLevel !== 'null';
            const riskLabels = { medium: 'Gemiddeld', high: 'Hoog', low: 'Laag' };
            const criticalBadge = isCritical
                ? `<span class="checkout-critical-badge checkout-critical-badge--${criticalLevel}">${riskLabels[criticalLevel] || criticalLevel}</span>`
                : '';
            const materialsList = document.getElementById('materials-list');
            const overviewBody = document.getElementById('materials-overview-body');
            const existingItem = materialsList.querySelector(`[data-material-id="${materialId}"]`);

            if (existingItem) {
                alert('Dit materiaal is al in de bestelling.');
                return;
            }

            const html = `
                <div class="material-edit-item" data-material-id="${materialId}">
                    <div class="material-edit-row">
                        <div class="material-info">
                            <span class="material-name">${materialName}</span>
                            ${criticalBadge}
                        </div>
                        <div class="material-controls">
                            <input 
                                type="number" 
                                name="materials[${materialId}]" 
                                value="1" 
                                min="1" 
                                max="10000"
                                class="material-quantity-input"
                                onchange="updateOverview()"
                            >
                            <button 
                                type="button" 
                                class="btn-remove-material" 
                                onclick="removeMaterial(${materialId})"
                                title="Verwijder materiaal"
                            >✕</button>
                        </div>
                    </div>
                </div>
            `;

            materialsList.insertAdjacentHTML('beforeend', html);

            const overviewHtml = `
                <tr data-material-id="${materialId}">
                    <td>
                        <span class="checkout-item-name">${materialName}</span>
                        ${criticalBadge}
                    </td>
                    <td class="table-right checkout-item-quantity">
                        <span id="qty-${materialId}">1</span>x
                    </td>
                </tr>
            `;

            overviewBody.insertAdjacentHTML('beforeend', overviewHtml);
            removedMaterials.delete(materialId);
            updateOverview();
            renderMaterialList();
        }

        function removeMaterial(materialId) {
            const item = document.querySelector(`.material-edit-item[data-material-id="${materialId}"]`);
            const overview = document.querySelector(`#materials-overview tbody tr[data-material-id="${materialId}"]`);
            
            if (item) item.remove();
            if (overview) overview.remove();
            
            removedMaterials.add(materialId);
            
            // Add hidden input to track removed materials
            const removedContainer = document.getElementById('removed-materials');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `removed_materials[${materialId}]`;
            input.value = '1';
            removedContainer.appendChild(input);

            updateOverview();
        }

        function updateOverview() {
            document.querySelectorAll('.material-edit-item').forEach(item => {
                const materialId = item.dataset.materialId;
                const quantity = item.querySelector('.material-quantity-input').value;
                const qtySpan = document.getElementById(`qty-${materialId}`);
                if (qtySpan) qtySpan.textContent = quantity;
            });
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('material-selector-modal');
            if (event.target === modal) {
                closeMaterialSelector();
            }
        });
    </script>

    <style>
        /* Material editing styles */
        .material-edit-item {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .material-edit-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .material-info {
            flex: 1;
            min-width: 0;
        }

        .material-name {
            display: block;
            font-weight: 600;
            color: #1f2937;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-bottom: 0.25rem;
        }

        .material-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .material-quantity-input {
            width: 60px;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 0.875rem;
            text-align: center;
        }

        .btn-remove-material {
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 4px;
            width: 32px;
            height: 32px;
            font-size: 1.25rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .btn-remove-material:hover {
            background: #dc2626;
        }

        .btn-add-material {
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.9375rem;
            transition: background 0.2s;
            width: 100%;
        }

        .btn-add-material:hover {
            background: #1d4ed8;
        }

        @media (max-width: 768px) {
            .material-edit-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .material-controls {
                width: 100%;
            }

            .material-quantity-input {
                flex: 1;
            }

            #material-selector-modal {
                padding: 0 !important;
            }

            #material-selector-modal > div {
                border-radius: 0 !important;
                max-height: 100vh !important;
            }
        }

        @media (max-width: 480px) {
            .material-edit-item {
                padding: 0.5rem;
            }

            .material-name {
                font-size: 0.875rem;
            }

            .material-quantity-input {
                width: 50px;
                font-size: 0.8rem;
                padding: 0.375rem;
            }

            .btn-remove-material {
                width: 28px;
                height: 28px;
                font-size: 1rem;
            }

            .btn-add-material {
                font-size: 0.875rem;
                padding: 0.625rem 0.75rem;
            }

            #material-selector-modal > div {
                padding: 1rem !important;
            }

            #material-selector-modal > div > div:first-child {
                margin-bottom: 1rem !important;
            }
        }
        
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
