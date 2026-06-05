<x-site-layout>
    <div class="container">
        <h1 class="page-title">Materiaal toevoegen</h1>

        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:24px;max-width:600px;">
            <form method="POST" action="{{ route('materialen.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Naam:</label>
                    <input type="text" name="naam" required class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Beschrijving:</label>
                    <textarea name="beschrijving" class="form-input" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Hoofdcategorie:</label>
                    <select id="category_id" class="form-input" onchange="filterSubcategories()">
                        <option value="">Kies een categorie</option>
                        @foreach($categorieen as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->naam }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Subcategorie:</label>
                    <select name="materiaal_subcategorie_id" id="subcategory_id" required class="form-input">
                        <option value="">Kies eerst een categorie</option>
                        @foreach($subcategorieen as $sub)
                            <option value="{{ $sub->id }}" data-category-id="{{ $sub->materiaal_categorie_id }}">
                                {{ $sub->naam }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;font-weight:500;color:#374151;">
                        <input type="checkbox" name="belangrijk" style="width:18px;height:18px;">
                        Belangrijk materiaal
                    </label>
                </div>

                <div style="display:flex;gap:8px;margin-top:24px;">
                    <button type="submit" class="search-button">Opslaan</button>
                    <a href="{{ route('materialen.beheer') }}" style="height:42px;display:inline-flex;align-items:center;background:#475569;color:#fff;text-decoration:none;padding:0 16px;border-radius:6px;font-weight:600;font-size:13px;">Annuleren</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function filterSubcategories() {
            const catId = document.getElementById('category_id').value;
            const subSelect = document.getElementById('subcategory_id');
            const options = subSelect.querySelectorAll('option');

            options.forEach(opt => {
                if (opt.value === '') {
                    opt.hidden = false;
                    opt.text = catId ? 'Kies een subcategorie' : 'Kies eerst een categorie';
                    return;
                }
                opt.hidden = catId && opt.dataset.categoryId !== catId;
            });

            if (subSelect.value && document.querySelector(`#subcategory_id option[value="${subSelect.value}"]`)?.hidden) {
                subSelect.value = '';
            }
        }
    </script>
</x-site-layout>