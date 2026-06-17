<x-site-layout>
    <div class="container">
        <h1 class="page-title">Nieuwe rol</h1>

        <form action="{{ route('roles.store') }}" method="post" class="user-form">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Naam</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required>
                @error('name') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn-primary">Aanmaken</button>
                <a href="{{ route('roles.index') }}" class="btn-cancel">Annuleren</a>
            </div>
        </form>
    </div>
</x-site-layout>