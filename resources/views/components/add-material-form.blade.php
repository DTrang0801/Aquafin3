<div class="add-material-section">
    <h3 class="section-title add-material-section__title">Snel nieuw materiaal toevoegen</h3>
    <p class="card-subtitle add-material-section__subtitle">
        Voeg meteen een nieuw materiaal toe en koppel het als kritiek item indien nodig.
    </p>

    @if ($errors->has('add_material'))
        <div class="weather-alert weather-alert--danger">
            {{ $errors->first('add_material') }}
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

<style>
.add-material-section {
    border-top: 1px solid rgba(51, 65, 85, 0.5);
    margin-top: 20px;
    padding-top: 20px;
}

.add-material-section__title {
    font-size: 14px;
    font-weight: 700;
    color: #cbd5e1;
}

.add-material-section__subtitle {
    margin-bottom: 16px;
    text-align: left;
}

.add-material-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-label {
    color: #cbd5e1;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
}

.form-input {
    background-color: rgba(51, 65, 85, 0.4);
    border: 1px solid rgba(51, 65, 85, 0.6);
    border-radius: 6px;
    color: #f1f5f9;
    font-size: 14px;
    padding: 10px 12px;
    transition: border 0.2s ease;
}

.form-input:focus {
    border-color: #0891b2;
    outline: none;
}

.form-input--error {
    border-color: #ef4444;
}

.form-input--textarea {
    resize: vertical;
    font-family: inherit;
}

.form-error {
    color: #f87171;
    font-size: 12px;
}

.checkbox-label {
    align-items: center;
    cursor: pointer;
    display: flex;
    gap: 8px;
}

.checkbox-label input[type="checkbox"] {
    cursor: pointer;
}

.checkbox-label__text {
    color: #cbd5e1;
    font-size: 14px;
}

.btn-add-material {
    background-color: #0891b2;
    border: none;
    border-radius: 8px;
    color: white;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    padding: 10px 18px;
    transition: background 0.2s ease;
}

.btn-add-material:hover {
    background-color: #0e7490;
}

.btn-add-material:active {
    background-color: #155e75;
}
</style>
