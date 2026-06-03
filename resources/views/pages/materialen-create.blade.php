<h1>Materiaal toevoegen</h1>

<form method="POST" action="/materialen">
    @csrf

    <label>Naam:</label>
    <input type="text" name="naam">

    <br><br>

    <label>Beschrijving:</label>
    <textarea name="beschrijving"></textarea>

    <br><br>

    <label>Subcategorie:</label>
    <select name="materiaal_subcategorie_id">
        @foreach($subcategorieen as $subcategorie)
            <option value="{{ $subcategorie->id }}">
                {{ $subcategorie->naam }}
            </option>
        @endforeach
    </select>

    <br><br>

    <label>
        <input type="checkbox" name="belangrijk">
        Belangrijk materiaal
    </label>

    <br><br>

    <button type="submit">Opslaan</button>
</form>