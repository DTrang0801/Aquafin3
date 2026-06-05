<x-site-layout>
    <div class="container">
        <h1 class="page-title">Rollen beheer</h1>

        @if(session('success'))
            <p style="color:green">{{ session('success') }}</p>
        @endif
        @if(session('error'))
            <p style="color:red">{{ session('error') }}</p>
        @endif

        <a href="{{ route('admin.roles.create') }}">+ Nieuwe rol aanmaken</a>

        <table border="1" cellpadding="8" style="margin-top: 16px; width: 100%">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Aantal gebruikers</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td>
                            @if($role->users_count === 0)
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="post" style="display:inline">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" onclick="return confirm('Zeker weten?')">Verwijderen</button>
                                </form>
                            @else
                                <span style="color:gray">Kan niet verwijderen ({{ $role->users_count }} gebruiker(s))</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Geen rollen gevonden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-site-layout>