<x-site-layout>
    <div class="container">
        <h1 class="page-title">Mijn Mandje</h1>

        @if(session('success'))
            <div class="alert-box success-box" style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                {{ session('success') }}
            </div>
        @endif

        @if($materialen->isEmpty())
            <div class="no-data-box" style="text-align: center; padding: 4px 0;">
                <p>Je mandje is momenteel leeg.</p>
                <a href="{{ route('materialen') }}" class="btn-primary" style="text-decoration: none; padding: 8px 15px; display: inline-block; margin-top: 10px;">Terug naar Materialen</a>
            </div>
        @else
            <table class="custom-table" style="width: 100%; margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th>Materiaal</th>
                        <th>Subcategorie</th>
                        <th style="width: 150px;">Aantal</th>
                        <th style="width: 120px;">Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materialen as $item)
                        <tr>
                            <td class="font-bold">
                                {{ $item->naam }}
                                @if($item->belangrijk)
                                    <span style="color: #e53e3e; margin-left: 5px;">⚠️</span>
                                @endif
                            </td>
                            <td class="text-italic">{{ $item->subcategorie->naam ?? 'N/A' }}</td>
                            <td>
                                <form action="{{ route('winkelmandje.update', $item->id) }}" method="POST" style="display: flex; gap: 5px;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="aantal" value="{{ $item->pivot->aantal }}" min="1" style="width: 60px; text-align: center; padding: 4px;">
                                    <button type="submit" style="background-color: #4a5568; color: white; border: none; padding: 4px 8px; cursor: pointer; border-radius: 3px;">Bijwerken</button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('winkelmandje.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background-color: #e53e3e; color: white; border: none; padding: 6px 12px; cursor: pointer; border-radius: 3px;" onclick="return confirm('Weet je zeker dat je dit item wilt verwijderen?')">Verwijderen</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <a href="{{ route('materialen') }}" style="color: #3182ce; text-decoration: none;">← Verder Winkelen</a>
                <a href="#" class="btn-checkout" style="background-color: #3182ce; color: white; padding: 10px 20px; text-decoration: none; font-weight: bold; border-radius: 4px;">Bestelling Afronden →</a>
            </div>
        @endif
    </div>
</x-site-layout>