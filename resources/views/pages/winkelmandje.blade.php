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
                <a href="{{ route('winkelmandje.checkout') }}" class="btn-checkout" style="background-color: #3182ce; color: white; padding: 10px 20px; text-decoration: none; font-weight: bold; border-radius: 4px;">Bestelling Afronden →</a>
            </div>
        @endif

        <div style="margin-top:24px;padding:16px;background:#fff3cd;border:1px solid #ffc107;border-radius:8px;">
            <p style="margin:0 0 8px 0;font-weight:600;color:#856404;">💡 Vergeet geen gasdetectiemateriaal!</p>
            <p style="margin:0 0 10px 0;color:#856404;font-size:14px;">Gasdetectiemeter (O₂, CH₄, H₂S, CO) — essentieel voor veilige inspecties.</p>
            <form action="{{ route('winkelmandje.add') }}" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="materiaal_id" value="59">
                <input type="hidden" name="aantal" value="1">
                <button type="submit" style="background:#2563eb;color:#fff;border:none;padding:8px 16px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">➕ Toevoegen aan mandje</button>
            </form>
        </div>
    </div>

    <style>
        .winkelmandje-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 20px 60px;
            color: #1f2937;
        }

        .winkelmandje-title {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .winkelmandje-subtitle {
            margin-bottom: 30px;
            color: #4b5563;
        }

        .winkelmandje-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            align-items: start;
        }

        .producten-lijst {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .product-kaart,
        .samenvatting-kaart {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .product-kaart {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .product-info h2 {
            font-size: 1.2rem;
            margin-bottom: 8px;
        }

        .product-info p {
            color: #6b7280;
            margin: 0;
        }

        .product-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
            text-align: right;
            font-weight: 600;
            color: #111827;
        }

        .samenvatting-kaart h2 {
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .samenvatting-regel {
            display: flex;
            justify-content: space-between;
            margin-bottom: 14px;
            color: #374151;
        }

        .samenvatting-regel.totaal {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #d1d5db;
            font-size: 1.1rem;
            font-weight: 700;
            color: #111827;
        }

        .bestel-btn {
            width: 100%;
            margin-top: 20px;
            padding: 12px 16px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
        }

        .bestel-btn:hover {
            background-color: #1d4ed8;
        }

        @media (max-width: 768px) {
            .winkelmandje-layout {
                grid-template-columns: 1fr;
            }

            .product-kaart {
                flex-direction: column;
            }

            .product-details {
                text-align: left;
            }
        }
    </style>
</x-site-layout>