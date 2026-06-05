<div class="add-material-section weather-card">
    <div class="section-header">
        <h2 class="section-heading">Snel nieuw materiaal toevoegen</h2>
        <p class="section-description">
            Voeg meteen een nieuw materiaal toe en koppel het als kritiek item indien nodig.
        </p>
    </div>

    @if ($errors->has('add_material'))
        <div class="weather-alert weather-alert--danger">
            <span class="alert-icon">✕</span>
            <span>{{ $errors->first('add_material') }}</span>
        </div>
    @endif

    <form action="{{ route('weersvoorspelling.addMaterial') }}" method="POST" class="add-material-form">
        @csrf

        <div class="form-group">
            <label for="naam" class="form-label">Naam</label>
            <input
                type="text"
                id="naam"
                name="naam"
                placeholder="Pomp A, Opslag B, etc."
                required
                class="form-input @error('naam') form-input--error @enderror"
                value="{{ old('naam') }}"
            >
            @error('naam')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="materiaal_subcategorie_id" class="form-label">Subcategorie</label>
            <select
                id="materiaal_subcategorie_id"
                name="materiaal_subcategorie_id"
                required
                class="form-input @error('materiaal_subcategorie_id') form-input--error @enderror"
            >
                <option value="">-- Selecteer subcategorie --</option>
                @foreach ($subcategorieen as $categorieName => $subcategories)
                    <optgroup label="{{ $categorieName }}">
                        @foreach ($subcategories as $subcategorie)
                            <option
                                value="{{ $subcategorie->id }}"
                                @selected(old('materiaal_subcategorie_id') == $subcategorie->id)
                            >
                                {{ $subcategorie->naam }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @error('materiaal_subcategorie_id')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="beschrijving" class="form-label">Beschrijving (optioneel)</label>
            <textarea
                id="beschrijving"
                name="beschrijving"
                placeholder="Optionele details..."
                rows="3"
                class="form-input form-input--textarea @error('beschrijving') form-input--error @enderror"
            >{{ old('beschrijving') }}</textarea>
            @error('beschrijving')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <label class="checkbox-label">
            <input
                type="checkbox"
                name="link_as_critical"
                value="1"
                @checked(old('link_as_critical'))
            >
            <span class="checkbox-label__text">Onmiddellijk als kritiek item koppelen</span>
        </label>

        <button type="submit" class="btn-add-material">Toevoegen en opslaan</button>
    </form>
</div>
