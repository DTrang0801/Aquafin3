<x-site-layout>
    <div class="container">
        <h1 class="page-title">Gebruikersbeheer</h1>

        @if (session('succes'))
            <p style="color:green">{{ session('succes') }}</p>   
        @endif

        <a href="{{ route('gebruikers.create') }}" class="btn-primary" style="display:inline-block;padding:8px 18px;text-decoration:none;">+ Nieuwe gebruiker</a>

        <table class="custom-table" style="margin-top: 16px;">
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
                        <td style="white-space:nowrap;">
                            <a href="{{ route('gebruikers.edit', $user) }}" class="btn-primary" style="padding:4px 12px;text-decoration:none;font-size:13px;margin-right:6px;">Bewerken</a>
                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('gebruikers.destroy', $user) }}" method="post" style="display:inline;">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" onclick="return confirm('Wilt u deze gebruiker verwijderen?')" class="btn-primary" style="padding:4px 12px;font-size:13px;background-color:#dc2626;width:auto;">Verwijderen</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
</x-site-layout>