<x-site-layout>
    <div class="container">

        <h1 class="page-title">Meest bestelde materialen</h1>

        @if($mostOrderedItems->isEmpty())
            <div style="text-align: center; padding: 40px; color: #6b7280;">
                <p style="font-size: 16px; margin-bottom: 10px;">Geen bestellingen gevonden</p>
                <p style="font-size: 14px; color: #9ca3af;">Er zijn nog geen items besteld.</p>
            </div>
        @else
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">Nr.</th>
                        <th>Naam</th>
                        <th>Categorie</th>
                        <th>Subcategorie</th>
                        <th style="width: 120px; text-align: right;">Aantal besteld</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($mostOrderedItems as $index => $item)
                        <tr>
                            <td style="text-align: center; font-weight: bold;">
                                {{ $index + 1 }}
                            </td>
                            <td class="font-bold">{{ $item->naam }}</td>
                            <td>{{ $item->subcategorie?->categorie?->naam ?? 'N/A' }}</td>
                            <td>{{ $item->subcategorie?->naam ?? 'N/A' }}</td>
                            <td style="text-align: right; font-weight: bold; color: #dc2626;">
                                <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 20px; font-size: 14px; display: inline-block; min-width: 50px; text-align: center;">
                                    {{ $item->order_count }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>
</x-site-layout>
