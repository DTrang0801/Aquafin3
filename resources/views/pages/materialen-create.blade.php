<x-site-layout>
    <div class="container">
        <h1 class="page-title">Materiaal toevoegen</h1>

        <div class="alert-box">
            <form method="POST" action="/materialen">
                @csrf

                <label>Naam:</label>
                <input type="text" name="naam" required>

                <br><br>

                <label>Beschrijving:</label>
                <textarea name="beschrijving"></textarea>

                <br><br>

                <label>Subcategorie:</label>
                <select name="materiaal_subcategorie_id" required>
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
        </div>
    </div>
</x-site-layout>