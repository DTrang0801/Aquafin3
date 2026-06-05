<x-site-layout>
    <div class="container">
        <h1 class="page-title">Nieuwe gebruiker</h1>

        <form action="{{ route('gebruikers.store') }}" method="post" class="user-form">
            @csrf

            <label for="name">Naam</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            @error('name') <p class="alert-error">{{ $message }}</p> @enderror

            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
            @error('email') <p class="alert-error">{{ $message }}</p> @enderror

            <label for="password">Wachtwoord</label>
            <input type="password" name="password" id="password" required>
            @error('password') <p class="alert-error">{{ $message }}</p> @enderror

            <label for="role">Rol</label>
            <select name="role" id="role" required onchange="toggleProvinceField()">
                <option value="">-- Kies een rol --</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="stockbeheerder" {{ old('role') == 'stockbeheerder' ? 'selected' : '' }}>Stockbeheerder</option>
                <option value="technieker" {{ old('role') == 'technieker' ? 'selected' : '' }}>Technieker</option>
            </select>
            @error('role') <p class="alert-error">{{ $message }}</p> @enderror

            <div id="province-field" style="display: {{ old('role') == 'technieker' ? 'block' : 'none' }};">
                <label for="province">Provincie (alleen voor technikers)</label>
                <select name="province" id="province">
                    <option value="">-- Geen provincie --</option>
                    <option value="Vlaams-Brabant" {{ old('province') == 'Vlaams-Brabant' ? 'selected' : '' }}>Vlaams-Brabant</option>
                    <option value="West-Vlaanderen" {{ old('province') == 'West-Vlaanderen' ? 'selected' : '' }}>West-Vlaanderen</option>
                    <option value="Oost-Vlaanderen" {{ old('province') == 'Oost-Vlaanderen' ? 'selected' : '' }}>Oost-Vlaanderen</option>
                    <option value="Limburg" {{ old('province') == 'Limburg' ? 'selected' : '' }}>Limburg</option>
                    <option value="Antwerpen" {{ old('province') == 'Antwerpen' ? 'selected' : '' }}>Antwerpen</option>
                </select>
                @error('province') <p class="alert-error">{{ $message }}</p> @enderror
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn-save">Aanmaken</button>
                <a href="{{ route('gebruikers') }}" class="btn-cancel">Annuleren</a>
            </div>
        </form>
    </div>

    <script>
        function toggleProvinceField() {
            const role = document.getElementById('role').value;
            const provinceField = document.getElementById('province-field');
            provinceField.style.display = role === 'technieker' ? 'block' : 'none';
        }
    </script>
</x-site-layout>