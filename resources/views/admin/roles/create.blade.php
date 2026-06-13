<x-site-layout>
    <div class="container">
        <h1 class="page-title">Nieuwe rol</h1>

        <form action="{{ route('roles.store') }}" method="post">
            @csrf

            <label for="name">Naam</label><br>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required><br>
            @error('name') <p style="color:red">{{ $message }}</p> @enderror

            <div style="margin-top: 20px;">
                <button type="submit">Aanmaken</button>
                <a href="{{ route('roles.index') }}">Annuleren</a>
            </div>
        </form>
    </div>
</x-site-layout>