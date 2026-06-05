<x-site-layout>
    <div class="container">

        <h1 class="page-title">Meest bestelde materialen</h1>

        @if($mostOrderedItems->isEmpty())
            <div class="empty-state">
                <p class="empty-state-title">Geen bestellingen gevonden</p>
                <p class="empty-state-subtitle">Er zijn nog geen items besteld.</p>
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
                            <td class="table-center font-bold">
                                {{ $index + 1 }}
                            </td>
                            <td class="font-bold">{{ $item->naam }}</td>
                            <td>{{ $item->subcategorie?->categorie?->naam ?? 'N/A' }}</td>
                            <td>{{ $item->subcategorie?->naam ?? 'N/A' }}</td>
                            <td class="table-right">
                                <span class="badge badge-important">
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
