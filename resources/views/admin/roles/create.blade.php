<x-site-layout>
    <div class="container">
        <h1 class="page-title">Nieuwe rol aanmaken</h1>

        <form action="{{ route('admin.roles.store') }}" method="post">
            @csrf

            <label for="name">Naam van de rol</label><br>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required><br>
            @error('name')
                <p style="color:red">{{ $message }}</p>
            @enderror

            <br>
            <button type="submit">Aanmaken</button>
            <a href="{{ route('admin.roles.index') }}">Annuleren</a>
        </form>
    </div>
</x-site-layout>