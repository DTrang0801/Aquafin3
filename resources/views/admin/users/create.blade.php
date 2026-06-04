<x-site-layout>
    <div class="container">
        <h1>Nieuwe gebruiker</h1>

        <form action="{{ route('gebruikers.store') }}" method="post">
            @csrf

            <label for="name">Naam</label><br>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required><br>
            @error('name') <p style="color:red">{{ $message }}</p> @enderror

            <label for="email">Email</label><br>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required><br>
            @error('email') <p style="color:red">{{ $message }}</p> @enderror

            <label for="password">Wachtwoord</label><br>
            <input type="password" name="password" id="password" required><br>
            @error('password') <p style="color:red">{{ $message }}</p> @enderror

            <label for="role">Rol</label><br>
            <select name="role" id="role" required>
                <option value="">-- Kies een rol --</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="stockbeheerder" {{ old('role') == 'stockbeheerder' ? 'selected' : '' }}>Stockbeheerder</option>
                <option value="technieker" {{ old('role') == 'technieker' ? 'selected' : '' }}>Technieker</option>
            </select><br>
            @error('role') <p style="color:red">{{ $message }}</p> @enderror

            <br>
            <button type="submit">Aanmaken</button>
            <a href="{{ route('gebruikers') }}">Annuleren</a>
        </form>
    </div>
</x-site-layout>