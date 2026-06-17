<x-site-layout>
    <div class="container">
        <h1 class="page-title">Materialen</h1>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('materialen') }}" method="GET" id="search-filter-form" style="margin-bottom: 30px;">
                <div class="search-filter-grid">
                    
                    <div class="filter-group keyword-search"
                         x-data="{
                             query: '{{ request('search') }}',
                             suggestions: [],
                             show: false,
                             async fetch() {
                                 if (this.query.length < 1) {
                                     this.suggestions = [];
                                     this.show = false;
                                     return;
                                 }
                                 try {
                                     const res = await fetch('{{ route('materialen.suggesties') }}?q=' + encodeURIComponent(this.query));
                                     this.suggestions = await res.json();
                                     this.show = this.suggestions.length > 0;
                                 } catch {
                                     this.suggestions = [];
                                     this.show = false;
                                 }
                             },
                             select(name) {
                                 window.location.href = '{{ route('materialen') }}?search=' + encodeURIComponent(name) + '&suggestie=1';
                             },
                             hide() { setTimeout(() => this.show = false, 200) }
                         }">
                        <label for="search" class="filter-label">Zoeken op term</label>
                        <div style="position: relative; display: flex; width: 100%;">
                            <input type="text" id="search" name="search" class="search-input"
                                   placeholder="Zoek materiaalnaam..."
                                   x-model="query"
                                   x-on:input.debounce.300ms="fetch"
                                   x-on:blur="hide"
                                   x-on:focus="fetch"
                                   value="{{ request('search') }}">
                            @if(request('search'))
                                <a href="{{ route('materialen', request()->except('search')) }}" class="search-clear-btn">×</a>
                            @endif

                            <div x-show="show" x-cloak
                                 style="position: absolute; top: 100%; left: 0; right: 0; z-index: 50;
                                         background: #fff; border: 1px solid #475569; border-radius: 6px;
                                         margin-top: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                <template x-for="item in suggestions" :key="item.id">
                                    <button type="button" @click="select(item.naam)"
                                            style="display: block; width: 100%; text-align: left; padding: 10px 12px;
                                                   border: none; background: none; cursor: pointer; font-size: 14px;
                                                   color: #333; border-bottom: 1px solid #e5e5e5;
                                                   transition: background 0.15s;"
                                            @mouseenter="$el.style.background='#f0f0f0'"
                                            @mouseleave="$el.style.background='none'">
                                        <span x-text="item.naam" style="font-weight: 600;"></span>
                                        <span x-text="item.subcategorie?.naam ? ' (' + item.subcategorie.naam + ')' : ''"
                                              style="color: #888; font-size: 12px;"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="filter-group">
                        <label for="category_id" class="filter-label">Hoofdcategorie</label>
                        <select name="category_id" id="category_id" class="filter-select" onchange="document.getElementById('subcategory_id').value=''; this.form.submit();">
                            <option value="">Alle Categorieën</option>
                            @foreach($filterCategories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->naam }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="subcategory_id" class="filter-label">Subcategorie</label>
                        <select name="subcategory_id" id="subcategory_id" class="filter-select" onchange="this.form.submit();" {{ $filterSubcategories->isEmpty() ? 'disabled' : '' }}>
                            <option value="">Alle Subcategorieën</option>
                            @foreach($filterSubcategories as $sub)
                                <option value="{{ $sub->id }}" {{ request('subcategory_id') == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->naam }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group actions-row" style="display: flex; gap: 8px; align-items: flex-end;">
                        <button type="submit" class="search-button" style="width: 100%; height: 42px;">Zoek</button>
                        @if(request('search') || request('category_id') || request('subcategory_id'))
                            <a href="{{ route('materialen') }}" class="btn-reset" style="height: 42px; display: inline-flex; align-items: center; justify-content: center; background-color: #475569; color: white; text-decoration: none; padding: 0 16px; border-radius: 6px; font-weight: 600; font-size: 13px;">
                                X
                            </a>
                        @endif
                    </div>
                </div>
            </form>

        @if($belangrijkeMaterialen->isNotEmpty())
            <details class="category-block" style="margin-bottom: 25px; border-color: #ef4444;">
                
                <summary class="category-header" style="background: #ffd755; border-bottom: 1px solid #ef4444; cursor: pointer; user-select: none; display: flex; justify-content: space-between; align-items: center; padding: 14px 20px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="color: #3d3d3d; font-weight: bold; letter-spacing: 0.05em;">Belangrijk materiaal</span>
                    </div>
                    <span class="arrow" style="color: #fca5a5;">▼</span>
                </summary>

                <div class="category-content" style="padding: 0; background-color: rgba(15, 23, 42, 0.2);">
                    <table class="custom-table table-important" style="width: 100%; margin: 0;">
                        <tbody>
                            @foreach ($belangrijkeMaterialen as $materiaal)
                                <tr style="border-bottom: 1px solid #1e293b;">
                                    <td class="font-bold text-important" style="padding: 14px 20px; color: #000000; font-weight: 600;">
                                        @if($materiaal->foto)
                                            <img src="{{ asset('storage/' . $materiaal->foto) }}"
                                                style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;margin-bottom:8px;">
                                        @endif

                                                        <div>{{ $materiaal->naam }}</div>
                                        @if($materiaal->belangrijk)
                                            @php
                                                try {
                                                    $level = $materiaal->belangrijk instanceof \App\Enums\FloodRiskLevel
                                                        ? $materiaal->belangrijk
                                                        : \App\Enums\FloodRiskLevel::from((string)$materiaal->belangrijk);
                                                    echo '<span class="risk-badge risk-badge--' . $level->value . '">' . $level->label() . '</span>';
                                                } catch (\Exception $e) {
                                                    // Silently ignore invalid values
                                                }
                                            @endphp
                                        @endif
                                    </td>
                                    <td class="text-italic" style="padding: 14px 20px; color: #000000; font-style: italic;">
                                        {{ $materiaal->subcategorie->naam ?? 'N/A' }}
                                    </td>
                                    <td style="padding: 14px 20px; color: #000000; font-size: 14px;">
                                        {{ $materiaal->beschrijving ?? 'Geen beschrijving beschikbaar.' }}
                                    </td>
                                    <td>
                                        @if(Auth::user()?->role_id === \App\Models\Role::TECHNIEKER)
                                        <div class="add-to-cart-form" x-data="{
                                            quantidade: 1,
                                            isSubmitting: false,
                                            async addToCart(e) {
                                                e.preventDefault();
                                                this.isSubmitting = true;
                                                try {
                                                    const formData = new FormData();
                                                    formData.append('materiaal_id', {{ $materiaal->id }});
                                                    formData.append('aantal', this.quantidade);
                                                    formData.append('_token', '{{ csrf_token() }}');
                                                    
                                                    const response = await fetch('{{ route('winkelmandje.add') }}', {
                                                        method: 'POST',
                                                        body: formData
                                                    });
                                                    
                                                    if (response.ok) {
                                                        this.quantidade = 1;
                                                        showSuccessToast('Materiaal is toegevoegd aan je mandje!');
                                                        // Update cart badge
                                                        this.updateCartBadge();
                                                        // Show suggestions popup
                                                        showSuggestionsPopup({{ $materiaal->id }});
                                                    }
                                                } catch (error) {
                                                    console.error('Error adding to cart:', error);
                                                } finally {
                                                    this.isSubmitting = false;
                                                }
                                            },
                                            updateCartBadge() {
                                                fetch('{{ route('winkelmandje.count') }}')
                                                    .then(r => r.json())
                                                    .then(data => {
                                                        const badges = document.querySelectorAll('.nav-cart-badge');
                                                        const links = document.querySelectorAll('.nav-cart-link, .nav-cart-link-mobile');
                                                        
                                                        if (data.count > 0) {
                                                            badges.forEach(badge => badge.textContent = data.count);
                                                            // If no badge exists, create one
                                                            if (badges.length === 0) {
                                                                links.forEach(link => {
                                                                    const badge = document.createElement('span');
                                                                    badge.className = 'nav-cart-badge';
                                                                    badge.textContent = data.count;
                                                                    link.appendChild(badge);
                                                                });
                                                            }
                                                        } else {
                                                            // Remove all badges if count is 0
                                                            badges.forEach(badge => badge.remove());
                                                        }
                                                    });
                                            }
                                        }">
                                            <form @submit="addToCart" style="display: flex; gap: 6px; align-items: center; justify-content: flex-end;">
                                                <input type="number" x-model.number="quantidade" min="1" :disabled="isSubmitting">
                                                <button type="submit" class="btn-primary" :disabled="isSubmitting" x-text="isSubmitting ? '🔄' : '🛒 Voeg toe'"></button>
                                            </form>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        @endif

        @foreach ($categorieen as $categorie)
            @if(request('search') && !$openCategoryIds->contains($categorie->id))
                @continue
            @endif
            <details class="category-block" {{ (request('search') || request('category_id')) ? 'open' : '' }}>
                
                <summary class="category-header" style="cursor: pointer; user-select: none; display: flex; justify-content: space-between; align-items: center;">
                    <span>{{ $categorie->naam }}</span>
                    <span class="arrow">▼</span>
                </summary>

                <div class="category-content">
                    @foreach ($categorie->subcategorieen as $subcategorie)
                        @if(request('search') && !$openSubcategoryIds->contains($subcategorie->id))
                            @continue
                        @endif
                            <details class="subcategory-block" style="margin-bottom: 15px;" {{ (request('search') || request('subcategory_id')) ? 'open' : '' }}>
                            
                            <summary class="subcategory-title" style="cursor: pointer; user-select: none; list-style: none;">
                                <strong>{{ $subcategorie->naam }}</strong>
                                <span class="arrow">▼</span>

                            </summary>

                            <div style="margin-top: 10px;">
                                @if($subcategorie->materialen->isEmpty())
                                    <p class="no-data">Geen materialen in deze subcategorie.</p>
                                @else
                                    <table class="custom-table">
                                        <tbody>
                                            @foreach ($subcategorie->materialen as $materiaal)
                                                <tr>
                                                    <td class="font-bold">

                                                        @if($materiaal->foto)
                                                            <img src="{{ asset('storage/' . $materiaal->foto) }}"
                                                                style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;margin-bottom:8px;">
                                                        @endif

                                                        <div>{{ $materiaal->naam }}</div>

                                                    </td>
                                                    <td>{{ $materiaal->beschrijving ?? 'Geen beschrijving' }}</td>
                                                    <td>
                                                      <!-- <span class="badge {{ $materiaal->belangrijk ? 'badge-important' : 'badge-normal' }}">
                                                            {{ $materiaal->belangrijk ? 'Ja' : 'Nee' }}
                                                        </span> -->
                                                    </td>
                                    <td>
                                        @if(Auth::user()?->role_id === \App\Models\Role::TECHNIEKER)
                                        <div class="add-to-cart-form" x-data="{
                                            quantidade: 1,
                                            isSubmitting: false,
                                            async addToCart(e) {
                                                e.preventDefault();
                                                this.isSubmitting = true;
                                                try {
                                                    const formData = new FormData();
                                                    formData.append('materiaal_id', {{ $materiaal->id }});
                                                    formData.append('aantal', this.quantidade);
                                                    formData.append('_token', '{{ csrf_token() }}');
                                                    
                                                    const response = await fetch('{{ route('winkelmandje.add') }}', {
                                                        method: 'POST',
                                                        body: formData
                                                    });
                                                    
                                                    if (response.ok) {
                                                        this.quantidade = 1;
                                                        showSuccessToast('Materiaal is toegevoegd aan je mandje!');
                                                        // Update cart badge
                                                        this.updateCartBadge();
                                                        // Show suggestions popup
                                                        showSuggestionsPopup({{ $materiaal->id }});
                                                    }
                                                } catch (error) {
                                                    console.error('Error adding to cart:', error);
                                                } finally {
                                                    this.isSubmitting = false;
                                                }
                                            },
                                            updateCartBadge() {
                                                fetch('{{ route('winkelmandje.count') }}')
                                                    .then(r => r.json())
                                                    .then(data => {
                                                        const badges = document.querySelectorAll('.nav-cart-badge');
                                                        const links = document.querySelectorAll('.nav-cart-link, .nav-cart-link-mobile');
                                                        
                                                        if (data.count > 0) {
                                                            badges.forEach(badge => badge.textContent = data.count);
                                                            // If no badge exists, create one
                                                            if (badges.length === 0) {
                                                                links.forEach(link => {
                                                                    const badge = document.createElement('span');
                                                                    badge.className = 'nav-cart-badge';
                                                                    badge.textContent = data.count;
                                                                    link.appendChild(badge);
                                                                });
                                                            }
                                                        } else {
                                                            // Remove all badges if count is 0
                                                            badges.forEach(badge => badge.remove());
                                                        }
                                                    });
                                            }
                                        }">
                                            <form @submit="addToCart" style="display: flex; gap: 6px; align-items: center; justify-content: flex-end;">
                                                <input type="number" x-model.number="quantidade" min="1" :disabled="isSubmitting">
                                                <button type="submit" class="btn-primary" :disabled="isSubmitting" x-text="isSubmitting ? '🔄' : '🛒 Voeg toe'"></button>
                                            </form>
                                        </div>
                                        @endif
                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </details>
                    @endforeach
                </div>
            </details>
        @endforeach
    </div>

    <style>
        [x-cloak] { display: none !important; }
        .custom-table { table-layout: fixed; }
        .custom-table td:nth-child(1) { width: 22%; }
        .custom-table td:nth-child(2) { width: 38%; }
        .custom-table td:nth-child(3) { width: 12%; }
        .custom-table td:nth-child(4) { width: 28%; }
        .table-important td:nth-child(1) { width: 20%; }
        .table-important td:nth-child(2) { width: 18%; }
        .table-important td:nth-child(3) { width: 34%; }
        .table-important td:nth-child(4) { width: 28%; }
        .custom-table td:last-child form { display: flex; gap: 6px; align-items: center; justify-content: flex-end; }
        .custom-table td:last-child input[type="number"] { width: 60px; height: 34px; padding: 4px; text-align: center; border: 1px solid #475569; border-radius: 4px; }
        .custom-table td:last-child .btn-primary { padding: 6px 12px; height: 34px; cursor: pointer; border: none; border-radius: 4px; font-weight: 600; font-size: 13px; white-space: nowrap; }
        .table-important td:last-child input[type="number"] { background-color: #0f172a; color: white; }
        .table-important td:last-child .btn-primary { background-color: #ef4444; color: white; }
        .table-important td:last-child .btn-primary:hover { background-color: #dc2626; }
        .search-filter-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 16px;
            background-color:rgb(255, 255, 255);
            padding: 20px;
            border-radius: 12px;
            /* border: 2px solid rgb(77, 77, 77); */
            align-items: flex-end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-label {
            font-size: 12px;
            font-weight: 600;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .search-input, .filter-select {
            width: 100%;
            height: 42px;
            padding: 8px 12px;
            background-color:rgb(251, 251, 251);
            border: 1px solid #475569;
            border-radius: 6px;
            color:rgb(17, 17, 17);
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .search-input:focus, .filter-select:focus {
            border-color: #3b82f6;
        }

        .filter-select:disabled {
            background-color:rgb(200, 200, 200);
            color:rgb(65, 65, 65);
            border-color: #334155;
            cursor: not-allowed;
        }

        .search-clear-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
        }
        
        .search-clear-btn:hover { color: #f8fafc; }

        @media (max-width: 1200px) {
            .search-filter-grid {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .actions-row {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 768px) {
            .search-filter-grid {
                grid-template-columns: 1fr;
                gap: 12px;
                padding: 15px;
            }

            .actions-row {
                display: flex !important;
                gap: 8px;
            }

            .actions-row .search-button,
            .actions-row .btn-reset {
                flex: 1;
                height: 42px;
            }

            .custom-table {
                font-size: 13px;
            }

            .custom-table th,
            .custom-table td {
                padding: 12px 8px;
            }

            .custom-table td:nth-child(1) { width: auto; }
            .custom-table td:nth-child(2) { width: auto; }
            .custom-table td:nth-child(3) { width: auto; }
            .custom-table td:nth-child(4) { width: auto; }

            .custom-table td:last-child form {
                flex-direction: column;
                gap: 4px;
            }

            .custom-table td:last-child input[type="number"] {
                width: 100%;
                height: 36px;
                padding: 6px;
            }

            .custom-table td:last-child .btn-primary {
                width: 100%;
                height: 36px;
                padding: 1px;
                font-size: 11px;
            }

            .table-important td:nth-child(1) { width: auto; }
            .table-important td:nth-child(2) { width: auto; }
            .table-important td:nth-child(3) { width: auto; }
            .table-important td:nth-child(4) { width: auto; }

            .page-title {
                font-size: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .category-header {
                padding: 12px 16px;
                font-size: 0.9rem;
            }

            .category-content {
                padding: 12px;
            }
        }

        @media (max-width: 480px) {
            .search-filter-grid {
                padding: 12px;
            }

            .filter-select,
            .search-input {
                font-size: 16px;
                height: 44px;
            }

            .container {
                padding: 1rem 0.75rem;
            }

            .custom-table {
                font-size: 12px;
            }

            .custom-table th,
            .custom-table td {
                padding: 10px 6px;
            }

            .category-header {
                padding: 10px 12px;
                font-size: 0.85rem;
            }
        }
    </style>

    <style>
        .toast-success {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--success, #16a34a);
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: fadeIn 0.1s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <!-- Suggestions Popup Modal -->
    <div id="suggestions-overlay" class="suggestions-popup-overlay" style="display: none;"></div>
    <div id="suggestions-popup" class="suggestions-popup" style="display: none;">
        <div class="suggestions-popup-header">
            <h2 class="suggestions-popup-title">Aanbevolen Producten</h2>
            <button class="suggestions-popup-close" onclick="closeSuggestionsPopup()">✕</button>
        </div>
        <div class="suggestions-popup-content" id="suggestions-content"></div>
        <div style="padding: 12px 16px; text-align: center; border-top: 1px solid var(--border, #e5e7eb); display: flex; gap: 12px; justify-content: center;">
            <button onclick="closeSuggestionsPopup()" style="padding: 8px 24px; border-radius: 6px; border: none; background: #6b7280; color: #fff; font-weight: 700; cursor: pointer;">Nee, bedankt</button>
            <a href="{{ route('winkelmandje.index') }}" style="padding: 8px 24px; border-radius: 6px; border: none; background: #2563eb; color: #fff; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block;">Verder naar winkelmandje</a>
        </div>
    </div>

    <div id="success-toast" class="toast-success" style="display: none;"></div>

    <script>
        function showSuccessToast(message) {
            const toast = document.getElementById('success-toast');
            toast.textContent = message;
            toast.style.display = 'block';
            clearTimeout(toast._hideTimeout);
            toast._hideTimeout = setTimeout(() => {
                toast.style.display = 'none';
            }, 2500);
        }

        function showSuggestionsPopup(materiaalId) {
            const overlay = document.getElementById('suggestions-overlay');
            const popup = document.getElementById('suggestions-popup');
            
            fetch('/winkelmandje/suggestions/' + materiaalId)
                .then(r => r.json())
                .then(suggestions => {
                    const content = document.getElementById('suggestions-content');
                    content.innerHTML = '';
                    
                    if (suggestions.length === 0) {
                        content.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: var(--text-light); padding: 1rem;">Geen aanbevelingen beschikbaar</p>';
                    } else {
                        suggestions.forEach(item => {
                            const card = document.createElement('div');
                            card.className = 'suggestion-card';
                            
                            let imageHtml = '';
                            if (item.foto) {
                                imageHtml = `<img src="/storage/${item.foto}" alt="${item.naam}" class="suggestion-card-image">`;
                            }
                            
                            card.innerHTML = `
                                ${imageHtml}
                                <div class="suggestion-card-name">${item.naam}</div>
                                <div class="suggestion-card-actions">
                                    <input type="number" value="1" min="1" class="suggestion-quantity" data-id="${item.id}">
                                    <button type="button" onclick="addSuggestionToCart(${item.id}, this)" class="suggestion-add-btn">Voeg toe</button>
                                </div>
                            `;
                            
                            content.appendChild(card);
                        });
                    }
                    
                    overlay.style.display = 'block';
                    popup.style.display = 'block';
                });
        }
        
        function closeSuggestionsPopup() {
            const overlay = document.getElementById('suggestions-overlay');
            const popup = document.getElementById('suggestions-popup');
            overlay.style.display = 'none';
            popup.style.display = 'none';
        }
        
        function addSuggestionToCart(materiaalId, button) {
            const quantity = button.parentElement.querySelector('.suggestion-quantity').value;
            button.disabled = true;
            button.textContent = '🔄';
            
            const formData = new FormData();
            formData.append('materiaal_id', materiaalId);
            formData.append('aantal', quantity);
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch('{{ route('winkelmandje.add') }}', {
                method: 'POST',
                body: formData
            })
            .then(r => {
                if (r.ok) {
                    showSuccessToast('Materiaal is toegevoegd aan je mandje!');
                    button.textContent = '✓ Toegevoegd';
                    button.style.background = 'var(--success)';
                    setTimeout(() => {
                        button.disabled = false;
                        button.textContent = 'Voeg toe';
                        button.style.background = '';
                        // Update cart badge
                        fetch('{{ route('winkelmandje.count') }}')
                            .then(r => r.json())
                            .then(data => {
                                const badges = document.querySelectorAll('.nav-cart-badge');
                                const links = document.querySelectorAll('.nav-cart-link, .nav-cart-link-mobile');
                                
                                if (data.count > 0) {
                                    badges.forEach(badge => badge.textContent = data.count);
                                    if (badges.length === 0) {
                                        links.forEach(link => {
                                            const badge = document.createElement('span');
                                            badge.className = 'nav-cart-badge';
                                            badge.textContent = data.count;
                                            link.appendChild(badge);
                                        });
                                    }
                                }
                            });
                    }, 1500);
                } else {
                    button.disabled = false;
                    button.textContent = 'Voeg toe';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                button.disabled = false;
                button.textContent = 'Voeg toe';
            });
        }
        
        // Close popup when clicking on overlay
        document.getElementById('suggestions-overlay').addEventListener('click', closeSuggestionsPopup);
    </script>
</x-site-layout>