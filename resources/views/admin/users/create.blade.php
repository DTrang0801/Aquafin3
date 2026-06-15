<x-site-layout>
    <div class="container">
        <h1 class="page-title">Nieuwe gebruiker</h1>

        <form action="{{ route('gebruikers.store') }}" method="post" class="user-form">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Naam</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required>
                @error('name') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}" required>
                @error('email') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Wachtwoord</label>
                <input type="password" name="password" id="password" class="form-input" required>
                @error('password') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="role_id" class="form-label">Rol</label>
                <select name="role_id" id="role" class="form-input" required onchange="toggleProvinceField()">
                    <option value="">-- Kies een rol --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id? 'selected':''}}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id') <p class="text-danger">{{ $message }}</p> @enderror
            </div>
                
            <div id="province-field" class="form-group" style="display:none;">
                <label for="province" class="form-label">Provincie (alleen voor techniekers)</label>
                <select name="province" id="province" class="form-input">
                    <option value="">-- Geen provincie --</option>
                    <option value="Vlaams-Brabant" {{ old('province') == 'Vlaams-Brabant' ? 'selected' : '' }}>Vlaams-Brabant</option>
                    <option value="West-Vlaanderen" {{ old('province') == 'West-Vlaanderen' ? 'selected' : '' }}>West-Vlaanderen</option>
                    <option value="Oost-Vlaanderen" {{ old('province') == 'Oost-Vlaanderen' ? 'selected' : '' }}>Oost-Vlaanderen</option>
                    <option value="Limburg" {{ old('province') == 'Limburg' ? 'selected' : '' }}>Limburg</option>
                    <option value="Antwerpen" {{ old('province') == 'Antwerpen' ? 'selected' : '' }}>Antwerpen</option>
                </select>
                @error('province') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn-primary">Aanmaken</button>
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