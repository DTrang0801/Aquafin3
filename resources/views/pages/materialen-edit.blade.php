<x-site-layout>
    <div class="container">
        <h1 class="page-title">Materiaal wijzigen</h1>

        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:24px;max-width:600px;">
            <form method="POST" action="{{ route('materialen.update', $materiaal) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Naam:</label>
                    <input type="text" name="naam" value="{{ $materiaal->naam }}" required class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Beschrijving:</label>
                    <textarea name="beschrijving" class="form-input" rows="4">{{ $materiaal->beschrijving }}</textarea>
                </div>

                @if($materiaal->foto)
                    <div class="form-group">
                        <label class="form-label">Huidige foto:</label><br>

                        <img src="{{ asset('storage/' . $materiaal->foto) }}"
                            style="display:block;width:120px;height:auto;border-radius:8px;border:1px solid #e2e8f0;margin-bottom:10px;">

                        <button type="submit"
                                name="verwijder_foto"
                                value="1"
                                onclick="return confirm('Ben je zeker dat je deze foto wilt verwijderen?')"
                                style="background:#dc2626;color:#fff;padding:8px 14px;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">
                            🗑️ Foto verwijderen
                        </button>
                    </div>
                @endif

                <div class="form-group">
                    <label class="form-label">Nieuwe foto:</label>
                    <input type="file" name="foto" accept="image/*" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Subcategorie:</label>
                    <select name="materiaal_subcategorie_id" class="form-input">
                        @foreach($subcategorieen as $subcategorie)
                            <option value="{{ $subcategorie->id }}"
                                {{ $materiaal->materiaal_subcategorie_id == $subcategorie->id ? 'selected' : '' }}>
                                {{ $subcategorie->naam }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;font-weight:500;color:#374151;">
                        <input type="checkbox" name="belangrijk"
                            {{ $materiaal->belangrijk ? 'checked' : '' }}
                            style="width:18px;height:18px;">
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
</x-site-layout>