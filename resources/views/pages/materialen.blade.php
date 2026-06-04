<x-site-layout>
    <div class="container">
        <h1 class="page-title">Materialen</h1>

            <form action="{{ route('materialen') }}" method="GET">
                <div class="search-filter">
                <input type="text" name="search" class="search-input" placeholder="Zoek materialen..." value="{{ request('search') }}">
                <button type="submit" class="search-button">Zoeken</button>
                    @if(request('search'))
                        <a href="{{ route('materialen') }}">X</a>
                     @endif
                </div>
            </form>

        @if($belangrijkeMaterialen->isNotEmpty())
            <div class="alert-box important-box">
                <div class="alert-header">
                    <span class="alert-icon">⚠️</span>
                    <h2 class="alert-title">Belangrijke Aandachtspunten / Materialen</h2>
                </div>
                
                <table class="custom-table table-important">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>Subcategorie</th>
                            <th>Beschrijving</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($belangrijkeMaterialen as $materiaal)
                            <tr>
                                <td class="font-bold text-important">{{ $materiaal->naam }}</td>
                                <td class="text-italic">{{ $materiaal->subcategorie->naam ?? 'N/A' }}</td>
                                <td>{{ $materiaal->beschrijving ?? 'Geen beschrijving' }}</td>
                                <td>
                                    <form action="{{ route('winkelmandje.add') }}" method="POST" class="add-to-cart-form" style="display: flex; gap: 5px;">
                                        @csrf
                                        <input type="hidden" name="materiaal_id" value="{{ $materiaal->id }}">
                                        <input type="number" name="aantal" value="1" min="1" style="width: 60px; padding: 4px; text-align: center;">
                                        <button type="submit" class="btn-primary" style="padding: 4px 10px; cursor: pointer;">🛒 Voeg toe</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        
        @foreach ($categorieen as $categorie)
            <details open class="category-block">
                
                <summary class="category-header" style="cursor: pointer; user-select: none; display: flex; justify-content: space-between; align-items: center;">
                    <span>{{ $categorie->naam }}</span>
                    <span class="arrow">▼</span>
                </summary>

                <div class="category-content">
                    @foreach ($categorie->subcategorieen as $subcategorie)
                        
                        <details class="subcategory-block" style="margin-bottom: 15px;">
                            
                            <summary class="subcategory-title" style="cursor: pointer; user-select: none; list-style: none;">
                                <strong>→ {{ $subcategorie->naam }}</strong> <small style="color: #3182ce; margin-left: 10px;">(Klik om te tonen/verbergen)</small>
                            </summary>

                            <div style="margin-top: 10px;">
                                @if($subcategorie->materialen->isEmpty())
                                    <p class="no-data">Geen materialen in deze subcategorie.</p>
                                @else
                                    <table class="custom-table">
                                        <thead>
                                            <tr>
                                                <th>Naam</th>
                                                <th>Beschrijving</th>
                                                <th style="width: 120px;">Belangrijk?</th>
                                                <th style="width: 180px;">Bestellen</th> </tr>
                                        </thead>
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
                                                        <form action="{{ route('winkelmandje.add') }}" method="POST" style="display: flex; gap: 5px; align-items: center;">
                                                            @csrf
                                                            <input type="hidden" name="materiaal_id" value="{{ $materiaal->id }}">
                                                            <input type="number" name="aantal" value="1" min="1" style="width: 60px; padding: 4px; text-align: center;">
                                                            <button type="submit" class="btn-primary" style="padding: 4px 10px; cursor: pointer;">🛒 Voeg toe</button>
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
</x-site-layout>