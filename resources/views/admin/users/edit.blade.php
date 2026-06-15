<x-site-layout>
    <div class="container">
        <h1 class="page-title">Gebruiker bewerken</h1>

        <form action="{{ route('gebruikers.update', $user) }}" method="post" class="user-form">
            @csrf
            @method('put')

            <div class="form-group">
                <label for="name" class="form-label">Naam</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                @error('name') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                @error('email') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Nieuw wachtwoord (leeg = ongewijzigd)</label>
                <input type="password" name="password" id="password" class="form-input">
                @error('password') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="role_id" class="form-label">Rol</label>
                <select name="role_id" id="role_id" class="form-input" required onchange="toggleProvinceField()">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
                </select>
                @error('role_id') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div id="province-field" class="form-group" style="display: {{ old('role_id', $user->role_id) == $user->role?->id && $user->role_id === \App\Models\Role::TECHNIEKER ? 'block' : 'none' }};">
                <label for="province" class="form-label">Provincie (alleen voor technikers)</label>
                <select name="province" id="province" class="form-input">
                    <option value="">-- Geen provincie --</option>
                    <option value="Vlaams-Brabant" {{ old('province', $user->province) == 'Vlaams-Brabant' ? 'selected' : '' }}>Vlaams-Brabant</option>
                    <option value="West-Vlaanderen" {{ old('province', $user->province) == 'West-Vlaanderen' ? 'selected' : '' }}>West-Vlaanderen</option>
                    <option value="Oost-Vlaanderen" {{ old('province', $user->province) == 'Oost-Vlaanderen' ? 'selected' : '' }}>Oost-Vlaanderen</option>
                    <option value="Limburg" {{ old('province', $user->province) == 'Limburg' ? 'selected' : '' }}>Limburg</option>
                    <option value="Antwerpen" {{ old('province', $user->province) == 'Antwerpen' ? 'selected' : '' }}>Antwerpen</option>
                </select>
                @error('province') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn-primary">Opslaan</button>
                <a href="{{ route('gebruikers') }}" class="btn-cancel">Annuleren</a>
            </div>
        </form>
    </div>

    <script>
        function toggleProvinceField() {
            const role = document.getElementById('role_id');
            const selected = role.options[role.selectedIndex].text;
            const provinceField = document.getElementById('province-field');
            provinceField.style.display = selected === 'technieker' ? 'block' : 'none';
        }
    </script>
</x-site-layout>