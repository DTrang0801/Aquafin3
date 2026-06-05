<x-site-layout>
    <div class="container">
        <h1 class="page-title">Bestellingen Overzicht</h1>

        @if (session('succes'))
            <p class="alert-success">{{ session('succes') }}</p>
        @endif

        <table class="users-table">
            <thead>
                <tr>
                    <th>Technieker</th>
                    <th>Datum</th>
                    <th>Tijd</th>
                    <th>Locatie</th>
                    <th>Materialen</th>
                    <th>Opmerking</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bestellingen as $bestelling)
                    <tr>
                        <td>{{ $bestelling->gebruiker->name }}</td>
                        <td>{{ $bestelling->gevraagde_datum }}</td>
                        <td>{{ $bestelling->gevraagde_tijd }}</td>
                        <td>{{ $bestelling->locatie }}</td>
                        <td>
                            <ul style="margin:0;padding-left:18px;">
                                @foreach($bestelling->materialen as $materiaal)
                                    <li>{{ $materiaal->naam }} (x{{ $materiaal->pivot->aantal }})</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ $bestelling->opmerking ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:#718096;padding:24px;">Nog geen bestellingen van techniekers.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-site-layout>
