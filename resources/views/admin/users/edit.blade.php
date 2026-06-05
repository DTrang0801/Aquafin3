<x-site-layout>
    <div class="container">
        <h1 class="page-title">Gebruiker bewerken</h1>

        <form action="{{ route('gebruikers.update', $user) }}" method="post" class="user-form">
            @csrf
            @method('put')

            <label for="name">Naam</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
            @error('name') <p class="alert-error">{{ $message }}</p> @enderror

            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
            @error('email') <p class="alert-error">{{ $message }}</p> @enderror

            <label for="password">Nieuw wachtwoord (leeg = ongewijzigd)</label>
            <input type="password" name="password" id="password">
            @error('password') <p class="alert-error">{{ $message }}</p> @enderror

            <label for="role">Rol</label>
            <select name="role" id="role" required>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="stockbeheerder" {{ old('role', $user->role) == 'stockbeheerder' ? 'selected' : '' }}>Stockbeheerder</option>
                <option value="technieker" {{ old('role', $user->role) == 'technieker' ? 'selected' : '' }}>Technieker</option>
            </select>
            @error('role') <p class="alert-error">{{ $message }}</p> @enderror

            <div style="margin-top: 20px;">
                <button type="submit" class="btn-save">Opslaan</button>
                <a href="{{ route('gebruikers') }}" class="btn-cancel">Annuleren</a>
            </div>
        </form>
    </div>
</x-site-layout>