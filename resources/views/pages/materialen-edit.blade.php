<x-site-layout>
    <div class="container">
        <h1 class="page-title">Materiaal wijzigen</h1>

        <form method="POST" action="{{ route('materialen.update', $materiaal) }}">
            @csrf
            @method('PUT')

            <label>Naam:</label>
            <input type="text" name="naam" value="{{ $materiaal->naam }}" required>

            <br><br>

            <label>Beschrijving:</label>
            <textarea name="beschrijving">{{ $materiaal->beschrijving }}</textarea>

            <br><br>

            <label>Subcategorie:</label>
            <select name="materiaal_subcategorie_id">
                @foreach($subcategorieen as $subcategorie)
                    <option value="{{ $subcategorie->id }}"
                        {{ $materiaal->materiaal_subcategorie_id == $subcategorie->id ? 'selected' : '' }}>
                        {{ $subcategorie->naam }}
                    </option>
                @endforeach
            </select>

            <br><br>

            <label>
                <input type="checkbox" name="belangrijk"
                    {{ $materiaal->belangrijk ? 'checked' : '' }}>
                Belangrijk materiaal
            </label>

            <br><br>

            <button type="submit">Opslaan</button>
        </form>
    </div>
</x-site-layout>