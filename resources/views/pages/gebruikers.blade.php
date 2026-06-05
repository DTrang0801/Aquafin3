<x-site-layout>
    <div class="container">
        <h1 class="page-title">Gebruikersbeheer</h1>

        @if (session('succes'))
            <p class="alert-success">{{ session('succes') }}</p>   
        @endif

        <a href="{{ route('gebruikers.create') }}" class="btn-add-user">+ Nieuwe gebruiker</a>

        <table class="users-table">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Provincie</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td>
                            @if ($user->role === 'technieker')
                                <span title="{{ $user->getDepotLocation() ?? 'Geen depot ingesteld' }}">
                                    {{ $user->province ?? 'Niet ingesteld' }}
                                </span>
                            @else
                                <span style="color: #999;">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions-cell">
                                <a href="{{ route('gebruikers.edit', $user) }}" class="btn-action btn-action-edit">Bewerken</a>
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('gebruikers.destroy', $user) }}" method="post" style="display:inline">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn-action btn-action-delete" onclick="return confirm('Zeker weten?')">Verwijderen</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
</x-site-layout>