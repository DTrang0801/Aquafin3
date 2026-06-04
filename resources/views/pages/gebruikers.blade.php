<x-site-layout>
    <div class="container">
        <h1 class="page-title">Gebruikersbeheer</h1>

        @if (session('succes'))
            <p style="color:green">{{ session('succes') }}</p>   
        @endif

        <a href="{{ route('gebruikers.create') }}">+ Nieuwe gebruiker</a>

        <table border="1" cellpadding="8" style="margin-top: 16px; width: 100%">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Email</th>
                    <th>Rol</th>
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
                            <a href="{{ route('gebruikers.edit', $user) }}">Bewerken</a>
                            |
                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('gebruikers.destroy', $user) }}" method="post" style="display:inline">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" onclick="return confirm('Zeker weten?')">Verwijderen</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
</x-site-layout>