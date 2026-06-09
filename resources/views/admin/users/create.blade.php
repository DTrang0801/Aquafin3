<x-site-layout>
    <div class="container">
        <h1 class="page-title">Nieuwe gebruiker</h1>

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

            <label for="role_id">Rol</label><br>
            <select name="role_id" id="role" required onchange="toggleProvinceField()">
                <option value="">-- Kies een rol --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id? 'selected':''}}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select><br>
            @error('role_id') <p style="color:red">{{ $message }}</p> @enderror
                
            <div id="province-field" style="display:none;">
                <label for="province">Provincie (alleen voor techniekers)</label>
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
            const select = document.getElementById('role');
            const selectedText = select.options[select.selectedIndex].text.toLowerCase();
            const provinceField = document.getElementById('province-field');
            provinceField.style.display = selectedText === 'technieker' ? 'block' : 'none';
        }
    </script>
</x-site-layout>