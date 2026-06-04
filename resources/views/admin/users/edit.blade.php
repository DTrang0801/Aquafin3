<x-site-layout>
    <div class="container">
        <h1>Gebruiker bewerken</h1>

        <form action="{{ route('gebruikers.update', $user) }}" method="post">
            @csrf
            @method('put')

            <label for="name">Naam</label><br>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required><br>
            @error('name') <p style="color:red">{{ $message }}</p> @enderror

            <label for="email">Email</label><br>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required><br>
            @error('email') <p style="color:red">{{ $message }}</p> @enderror

            <label for="password">Nieuw wachtwoord (leeg = ongewijzigd)</label><br>
            <input type="password" name="password" id="password"><br>
            @error('password') <p style="color:red">{{ $message }}</p> @enderror

            <label for="role">Rol</label><br>
            <select name="role" id="role" required>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="stockbeheerder" {{ old('role', $user->role) == 'stockbeheerder' ? 'selected' : '' }}>Stockbeheerder</option>
                <option value="technieker" {{ old('role', $user->role) == 'technieker' ? 'selected' : '' }}>Technieker</option>
            </select><br>
            @error('role') <p style="color:red">{{ $message }}</p> @enderror

            <br>
            <button type="submit">Opslaan</button>
            <a href="{{ route('gebruikers') }}">Annuleren</a>
        </form>
    </div>
</x-site-layout>