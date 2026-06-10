<x-site-layout>
    <div class="container">
        <h1 class="page-title">Gebruiker bewerken</h1>

        <form action="{{ route('gebruikers.update', $user) }}" method="post">
            @csrf
            @method('put')

            <label for="name">Naam</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required><br>
            @error('name') <p style="color:red">{{ $message }}</p> @enderror

            <label for="email">Email</label><br>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required><br>
            @error('email') <p style="color:red">{{ $message }}</p> @enderror

            <label for="password">Nieuw wachtwoord (leeg = ongewijzigd)</label><br>
            <input type="password" name="password" id="password"><br>
            @error('password') <p style="color:red">>{{ $message }}</p> @enderror

            <label for="role_id">Rol</label><br>
            <select name="role_id" id="role_id" required onchange="toggleProvinceField()">
            @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                    {{ $role->name }}
                </option>
            @endforeach
            </select>
            @error('role_id') <p class="alert-error">{{ $message }}</p> @enderror
            <div id="province-field" style="display: {{ old('role_id', $user->role_id) == $user->role?->id && $user->role_id === \App\Models\Role::TECHNIEKER ? 'block' : 'none' }};">                <label for="province">Provincie (alleen voor technikers)</label>
                <select name="province" id="province">
                    <option value="">-- Geen provincie --</option>
                    <option value="Vlaams-Brabant" {{ old('province', $user->province) == 'Vlaams-Brabant' ? 'selected' : '' }}>Vlaams-Brabant</option>
                    <option value="West-Vlaanderen" {{ old('province', $user->province) == 'West-Vlaanderen' ? 'selected' : '' }}>West-Vlaanderen</option>
                    <option value="Oost-Vlaanderen" {{ old('province', $user->province) == 'Oost-Vlaanderen' ? 'selected' : '' }}>Oost-Vlaanderen</option>
                    <option value="Limburg" {{ old('province', $user->province) == 'Limburg' ? 'selected' : '' }}>Limburg</option>
                    <option value="Antwerpen" {{ old('province', $user->province) == 'Antwerpen' ? 'selected' : '' }}>Antwerpen</option>
                </select>
                @error('province') <p class="alert-error">{{ $message }}</p> @enderror
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn-save">Opslaan</button>
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