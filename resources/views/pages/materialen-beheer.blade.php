<x-site-layout>
    <div class="container">
        <h1 class="page-title">Materiaal beheren</h1>

        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            {{-- Zoekformulier voor materialen --}}
            <form method="GET" action="{{ route('materialen.beheer') }}" style="display: flex; gap: 8px; align-items: center; flex: 1; max-width: 400px;">
                <input type="text" name="zoekterm" value="{{ $zoekterm ?? '' }}"
                    placeholder="Zoek op naam, beschrijving, categorie..."
                    style="flex: 1; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                <button type="submit" style="background: #2563eb; color: #fff; padding: 10px 20px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
                    Zoeken
                </button>
                @if($zoekterm)
                    <a href="{{ route('materialen.beheer') }}" style="background: #6b7280; color: #fff; padding: 10px 16px; border-radius: 8px; font-size: 14px; text-decoration: none; font-weight: 600;">
                        Wis
                    </a>
                @endif
            </form>

            <a href="{{ route('materialen.create') }}" style="background:#2563eb;color:#fff;padding:10px 24px;border-radius:8px;font-size:15px;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:6px;">
                + Nieuw materiaal
            </a>
        </div>

        @if($zoekterm)
            <p style="margin-bottom: 16px; color: #6b7280; font-size: 14px;">
                {{ $materialen->count() }} resultaat{{ $materialen->count() !== 1 ? 'en' : '' }} voor "{{ $zoekterm }}"
            </p>
        @endif

        <table class="custom-table">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Foto</th>
                    <th>Categorie</th>
                    <th>Subcategorie</th>
                    <th>Beschrijving</th>
                    <th style="width: 200px; text-align: right;">Acties</th>
                </tr>
            </thead>

            <tbody>
                @foreach($materialen as $materiaal)
                    <tr class="{{ $materiaal->belangrijk && is_string($materiaal->belangrijk) ? 'row-important row-important--' . $materiaal->belangrijk : '' }}">
                        <td class="font-bold">{{ $materiaal->naam }}</td>

                             <td>
                                @if($materiaal->foto)
                                    <img src="{{ asset('storage/' . $materiaal->foto) }}"
                                        style="width:60px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;">
                                @else
                                    -
                                @endif
                            </td>

                            <td>{{ $materiaal->subcategorie?->categorie?->naam ?? 'N/A' }}</td>
                            <td>{{ $materiaal->subcategorie->naam ?? 'N/A' }}</td>
                        <td>{{ Str::limit($materiaal->beschrijving, 60) ?: 'Geen beschrijving' }}</td>
                        <td>
                            <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                <a href="{{ route('materialen.edit', $materiaal) }}"
                                    style="background:#2563eb;color:#fff;padding:6px 14px;border-radius:6px;font-size:13px;text-decoration:none;font-weight:600;white-space:nowrap;">
                                    Wijzigen
                                </a>
                                <form method="POST" action="{{ route('materialen.destroy', $materiaal) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Ben je zeker dat je dit materiaal wilt verwijderen?')"
                                        style="background:#dc2626;color:#fff;padding:6px 14px;border-radius:6px;font-size:13px;border:none;cursor:pointer;font-weight:600;white-space:nowrap;">
                                        Verwijderen
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-site-layout>