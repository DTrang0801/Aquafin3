<x-site-layout>
    <div class="container">
        <h1 class="page-title">Rollenbeheer</h1>

        @if (session('success'))
            <div class="weather-alert weather-alert--ok">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="weather-alert weather-alert--danger">
                {{ session('error') }}
            </div>
        @endif

        <a href="{{ route('roles.create') }}" class="btn-add-user">+ Nieuwe rol</a>

        <table class="users-table">
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
                        <td class="table-right">
                            <a href="{{ route('roles.edit', $role) }}" class="btn-action btn-action-edit">Bewerken</a>
                            <form action="{{ route('roles.destroy', $role) }}" method="post" style="display:inline">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn-action btn-action-delete" onclick="return confirm('Zeker weten?')">Verwijderen</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            <a href="{{ route('gebruikers') }}" class="btn-cancel">← Terug naar gebruikers</a>
        </div>
    </div>
</x-site-layout>