<x-site-layout>
    <div class="container">
        <h1 class="page-title">Materiaal beheren</h1>

        <div style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
            <a href="{{ route('materialen.create') }}" style="background:#2563eb;color:#fff;padding:10px 24px;border-radius:8px;font-size:15px;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:6px;">
                + Nieuw materiaal
            </a>
        </div>

        <table class="custom-table">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Foto</th>
                    <th>Categorie</th>
                    <th>Subcategorie</th>
                    <th>Beschrijving</th>
                   <!-- <th>Belangrijk</th> -->
                    <th style="width: 200px;">Acties</th>
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
                            @if($materiaal->belangrijk && is_string($materiaal->belangrijk))
                                @php
                                    try {
                                        $level = $materiaal->belangrijk instanceof \App\Enums\FloodRiskLevel 
                                            ? $materiaal->belangrijk 
                                            : \App\Enums\FloodRiskLevel::from($materiaal->belangrijk);
                                        echo '<span class="risk-badge risk-badge--' . $level->value . '">' . $level->label() . '</span>';
                                    } catch (\Exception $e) {
                                        echo '<span style="color:#999; font-size:13px;">—</span>';
                                    }
                                @endphp
                            @else
                                <span style="color:#999; font-size:13px;">—</span>
                            @endif
                        </td>
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