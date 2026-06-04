<x-site-layout>
    <div class="container">
        <h1 class="page-title">Materialen</h1>

            <form action="{{ route('materialen') }}" method="GET">
                <div class="search-filter">
                <input type="text" name="search" class="search-input" placeholder="Zoek materialen..." value="{{ request('search') }}">
                <button type="submit" class="search-button">Zoeken</button>
                    @if(request('search'))
                        <a href="{{ route('materialen') }}" class="search-clear">×</a>
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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