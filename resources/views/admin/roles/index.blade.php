<x-site-layout>
    <div class="container">
        <h1 class="page-title">Rollenbeheer</h1>

        @if (session('success'))
            <p style="color:green">{{ session('success') }}</p>
        @endif
        @if (session('error'))
            <p style="color:red">{{ session('error') }}</p>
        @endif

        <a href="{{ route('roles.create') }}">+ Nieuwe rol</a>

        <table border="1" cellpadding="8" style="margin-top: 16px; width: 100%">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Aantal gebruikers</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td>
                            <a href="{{ route('roles.edit', $role) }}">Bewerken</a>
                            <form action="{{ route('roles.destroy', $role) }}" method="post" style="display:inline">
                                @csrf
                                @method('delete')
                                <button type="submit" onclick="return confirm('Zeker weten?')">Verwijderen</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            <a href="{{ route('gebruikers') }}">← Terug naar gebruikers</a>
        </div>
    </div>
</x-site-layout>