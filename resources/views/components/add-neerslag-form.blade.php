<div class="add-neerslag-section weather-card">
    <div class="section-header">
        <h2 class="section-heading">Neerslaggegevens toevoegen</h2>
        <p class="section-description">
            Voeg historische of bijgewerkte neerslaggegevens voor een maand toe aan de database.
        </p>
    </div>

    @if ($errors->any())
        <div class="weather-alert weather-alert--danger">
            <span class="alert-icon">✕</span>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form action="{{ route('weersvoorspelling.storeNeerslag') }}" method="POST" class="add-neerslag-form">
        @csrf

        <div class="form-group">
            <label for="jaar" class="form-label">Jaar</label>
            <input
                type="number"
                id="jaar"
                name="jaar"
                placeholder="2024"
                min="2004"
                max="{{ date('Y') }}"
                required
                class="form-input @error('jaar') form-input--error @enderror"
                value="{{ old('jaar') }}"
            >
            @error('jaar')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="maand" class="form-label">Maand</label>
            <select
                id="maand"
                name="maand"
                required
                class="form-input @error('maand') form-input--error @enderror"
            >
                <option value="">-- Selecteer maand --</option>
                <option value="1" @selected(old('maand') == 1)>Januari</option>
                <option value="2" @selected(old('maand') == 2)>Februari</option>
                <option value="3" @selected(old('maand') == 3)>Maart</option>
                <option value="4" @selected(old('maand') == 4)>April</option>
                <option value="5" @selected(old('maand') == 5)>Mei</option>
                <option value="6" @selected(old('maand') == 6)>Juni</option>
                <option value="7" @selected(old('maand') == 7)>Juli</option>
                <option value="8" @selected(old('maand') == 8)>Augustus</option>
                <option value="9" @selected(old('maand') == 9)>September</option>
                <option value="10" @selected(old('maand') == 10)>Oktober</option>
                <option value="11" @selected(old('maand') == 11)>November</option>
                <option value="12" @selected(old('maand') == 12)>December</option>
            </select>
            @error('maand')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="mm" class="form-label">Neerslag (mm)</label>
            <input
                type="number"
                id="mm"
                name="mm"
                placeholder="0"
                min="0"
                max="1000"
                step="1"
                required
                class="form-input @error('mm') form-input--error @enderror"
                value="{{ old('mm') }}"
            >
            @error('mm')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn-add-material">Opslaan</button>
    </form>
</div>
