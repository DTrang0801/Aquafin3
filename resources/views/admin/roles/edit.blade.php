<x-site-layout>
    <div class="container">
        <h1 class="page-title">Rol bewerken</h1>

        <form action="{{ route('roles.update', $role) }}" method="post" class="user-form">
            @csrf
            @method('put')

            <div class="form-group">
                <label for="name" class="form-label">Naam</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $role->name) }}" required>
                @error('name') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn-primary">Opslaan</button>
                <a href="{{ route('roles.index') }}" class="btn-cancel">Annuleren</a>
            </div>
        </form>
    </div>
</x-site-layout>