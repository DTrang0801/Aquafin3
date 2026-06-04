<x-site-layout>

    <a href="{{ route('materialen.create') }}">
    + Nieuw materiaal
    </a>
    
    <div class="container">
        <h1 class="page-title">Materiaal beheren</h1>

        <table class="custom-table">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Subcategorie</th>
                    <th>Beschrijving</th>
                    <th style="width: 250px;">Acties</th>
                </tr>
            </thead>

            <tbody>
                @foreach($materialen as $materiaal)
                    <tr>
                        <td>{{ $materiaal->naam }}</td>
                        <td>{{ $materiaal->subcategorie->naam ?? 'N/A' }}</td>
                        <td>{{ $materiaal->beschrijving }}</td>
                        <td>
                            <td style="white-space: nowrap;">
                                <a href="{{ route('materialen.edit', $materiaal) }}"
                                style="background:#2563eb;color:#fff;padding:8px 14px;border-radius:8px;font-size:14px;text-decoration:none;display:inline-block;margin-right:8px;">
                                    Wijzigen
                                </a>

                                <form method="POST" action="{{ route('materialen.destroy', $materiaal) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        onclick="return confirm('Ben je zeker dat je dit materiaal wilt verwijderen?')"
                                        style="background:#dc2626;color:#fff;padding:8px 14px;border-radius:8px;font-size:14px;border:none;cursor:pointer;">
                                        Verwijderen
                                    </button>
                                </form>
                            </td>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-site-layout>