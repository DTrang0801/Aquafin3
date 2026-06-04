<x-site-layout>
    <div class="container">
        <h1 class="page-title">Materialen</h1>

            <form action="{{ route('materialen') }}" method="GET" id="search-filter-form" style="margin-bottom: 30px;">
                <div class="search-filter-grid">
                    
                    <div class="filter-group keyword-search"
                         x-data="{
                             query: '{{ request('search') }}',
                             suggestions: [],
                             show: false,
                             async fetch() {
                                 if (this.query.length < 2) {
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
                                 this.query = name;
                                 this.show = false;
                                 $el.closest('form').submit();
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
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>

        <div class="favorieten-preview">
            <div class="fp-header">
                <span class="fp-title">⭐ Populaire materialen dit seizoen</span>
                <a href="{{ route('favorieten') }}" class="fp-link">Bekijk alle favorieten →</a>
            </div>
            <div class="fp-grid">
                <div class="fp-card">
                    <h4>PVC Buis 50mm</h4>
                    <p>Geschikt voor waterafvoer bij nat en koud weer.</p>
                    <form action="{{ route('winkelmandje.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="materiaal_id" value="118">
                        <input type="number" name="aantal" value="1" min="1">
                        <button type="submit">Voeg toe</button>
                    </form>
                </div>
                <div class="fp-card">
                    <h4>Waterdichte koppeling</h4>
                    <p>Populair voor lekvrije aansluitingen in regenachtige periodes.</p>
                    <form action="{{ route('winkelmandje.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="materiaal_id" value="119">
                        <input type="number" name="aantal" value="1" min="1">
                        <button type="submit">Voeg toe</button>
                    </form>
                </div>
                <div class="fp-card">
                    <h4>Afsluitklep</h4>
                    <p>Handig bij onderhoud en tijdelijke afsluiting van leidingen.</p>
                    <form action="{{ route('winkelmandje.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="materiaal_id" value="127">
                        <input type="number" name="aantal" value="1" min="1">
                        <button type="submit">Voeg toe</button>
                    </form>
                </div>
            </div>
        </div>

        @if($belangrijkeMaterialen->isNotEmpty())
            <details open class="category-block important-collapsible-block" style="margin-bottom: 25px; border-color: #ef4444;">
                
                <summary class="category-header" style="background: #ffd755; border-bottom: 1px solid #ef4444; cursor: pointer; user-select: none; display: flex; justify-content: space-between; align-items: center; padding: 14px 20px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 18px;">⚠️</span>
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
                                        {{ $materiaal->naam }}
                                    </td>
                                    <td class="text-italic" style="padding: 14px 20px; color: #000000; font-style: italic;">
                                        {{ $materiaal->subcategorie->naam ?? 'N/A' }}
                                    </td>
                                    <td style="padding: 14px 20px; color: #000000; font-size: 14px;">
                                        {{ $materiaal->beschrijving ?? 'Geen beschrijving beschikbaar.' }}
                                    </td>
                                    <td>
                                        <form action="{{ route('winkelmandje.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="materiaal_id" value="{{ $materiaal->id }}">
                                            <input type="number" name="aantal" value="1" min="1">
                                            <button type="submit" class="btn-primary">🛒 Voeg toe</button>
                                        </form>
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
            <details {{ $openCategoryIds->contains($categorie->id) ? 'open' : '' }} class="category-block">
                
                <summary class="category-header" style="cursor: pointer; user-select: none; display: flex; justify-content: space-between; align-items: center;">
                    <span>{{ $categorie->naam }}</span>
                    <span class="arrow">▼</span>
                </summary>

                <div class="category-content">
                    @foreach ($categorie->subcategorieen as $subcategorie)
                        @if(request('search') && !$openSubcategoryIds->contains($subcategorie->id))
                            @continue
                        @endif
                            <details {{ $openSubcategoryIds->contains($subcategorie->id) ? 'open' : '' }} class="subcategory-block" style="margin-bottom: 15px;">
                            
                            <summary class="subcategory-title" style="cursor: pointer; user-select: none; list-style: none;">
                                <strong>{{ $subcategorie->naam }}</strong>
                            </summary>

                            <div style="margin-top: 10px;">
                                @if($subcategorie->materialen->isEmpty())
                                    <p class="no-data">Geen materialen in deze subcategorie.</p>
                                @else
                                    <table class="custom-table">
                                        <tbody>
                                            @foreach ($subcategorie->materialen as $materiaal)
                                                <tr>
                                                    <td class="font-bold">{{ $materiaal->naam }}</td>
                                                    <td>{{ $materiaal->beschrijving ?? 'Geen beschrijving' }}</td>
                                                    <td>
                                                        <span class="badge {{ $materiaal->belangrijk ? 'badge-important' : 'badge-normal' }}">
                                                            {{ $materiaal->belangrijk ? 'Ja' : 'Nee' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('winkelmandje.add') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="materiaal_id" value="{{ $materiaal->id }}">
                                                            <input type="number" name="aantal" value="1" min="1">
                                                            <button type="submit" class="btn-primary">🛒 Voeg toe</button>
                                                        </form>
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
            background-color: #e5e5e5;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #334155;
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
            background-color: #ececec;
            border: 1px solid #475569;
            border-radius: 6px;
            color: #4b4b4b;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .search-input:focus, .filter-select:focus {
            border-color: #3b82f6;
        }

        .filter-select:disabled {
            background-color: #1e293b;
            color: #64748b;
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

        .favorieten-preview {
            background: linear-gradient(135deg, #fff9f0, #fff3e6);
            border: 1px solid #fde4c8;
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 25px;
        }
        .fp-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 8px; }
        .fp-title { font-weight: 700; font-size: 1.1rem; color: #b45309; }
        .fp-link { color: #2563eb; font-size: 0.9rem; font-weight: 600; text-decoration: none; }
        .fp-link:hover { text-decoration: underline; }
        .fp-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
        .fp-card { background: #fff; border-radius: 10px; padding: 14px 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
        .fp-card h4 { margin: 0 0 6px; font-size: 1rem; color: #1f2937; }
        .fp-card p { margin: 0 0 10px; font-size: 0.85rem; color: #6b7280; }
        .fp-card form { display: flex; gap: 6px; align-items: center; }
        .fp-card input[type="number"] { width: 50px; height: 32px; padding: 4px; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.85rem; }
        .fp-card button { padding: 6px 14px; cursor: pointer; background-color: #2563eb; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 0.85rem; white-space: nowrap; }
        .fp-card button:hover { background-color: #1d4ed8; }
        @media (max-width: 700px) { .fp-grid { grid-template-columns: 1fr; } }

        @media (max-width: 900px) {
            .search-filter-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
    </style>
</x-site-layout>