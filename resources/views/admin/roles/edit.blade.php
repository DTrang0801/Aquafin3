<x-site-layout>
    <div class="container">
        <h1 class="page-title">Rol bewerken</h1>

        <form action="{{ route('roles.update', $role) }}" method="post">
            @csrf
            @method('put')

            <label for="name">Naam</label><br>
            <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required><br>
            @error('name') <p style="color:red">{{ $message }}</p> @enderror

            <div style="margin-top: 20px;">
                <button type="submit">Opslaan</button>
                <a href="{{ route('roles.index') }}">Annuleren</a>
            </div>
        </form>
    </div>
</x-site-layout>