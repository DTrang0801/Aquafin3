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
            <div class="cart-table-wrapper">
                <table class="custom-table" style="width: 100%; margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th>Materiaal</th>
                            <th>Categorie</th>
                            <th style="width: 150px;">Aantal</th>
                            <th style="width: 120px;">Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materialen as $item)
                            <tr class="cart-item-row">
                                <td class="font-bold" data-label="Materiaal">
                                    {{ $item->naam }}
                                    @if($item->belangrijk)
                                        <span style="color: #e53e3e; margin-left: 5px;">⚠️</span>
                                    @endif
                                </td>
                                <td class="text-italic" data-label="Categorie">{{ $item->subcategorie->naam ?? 'N/A' }}</td>
                                <td data-label="Aantal">
                                    <form action="{{ route('winkelmandje.update', $item->id) }}" method="POST" class="quantity-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="aantal" value="{{ $item->pivot->aantal }}" min="1" class="quantity-input" data-item-id="{{ $item->id }}">
                                    </form>
                                </td>
                                <td data-label="Acties">
                                    <form action="{{ route('winkelmandje.destroy', $item->id) }}" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-button" onclick="return confirm('Weet je zeker dat je dit item wilt verwijderen?')">Verwijderen</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="cart-actions">
                <a href="{{ route('materialen') }}" class="cart-back-link">← Verder Winkelen</a>
                <a href="{{ route('winkelmandje.checkout') }}" class="btn-checkout">Bestelling Afronden →</a>
            </div>
        @endif

        <div style="margin-top:24px;padding:16px;background:#fff3cd;border:1px solid #ffc107;border-radius:8px;">
            <p style="margin:0 0 8px 0;font-weight:600;color:#856404;">⚠️ Vergeet geen gasdetectiemateriaal!</p>
            <form action="{{ route('winkelmandje.add') }}" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="materiaal_id" value="59">
                <input type="hidden" name="aantal" value="1">
                <button type="submit" style="background:#2563eb;color:#fff;border:none;padding:8px 16px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;width:100%;">➕ Toevoegen aan mandje</button>
            </form>
        </div>
    </div>

    <style>
        .cart-table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 16px;
        }

        .quantity-form {
            display: flex;
            gap: 6px;
            align-items: center;
            flex-wrap: wrap;
        }

        .quantity-input {
            width: 55px;
            text-align: center;
            padding: 5px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 13px;
            height: 34px;
        }

        .quantity-input:focus {
            outline: none;
            border-color: #3182ce;
            box-shadow: 0 0 0 2px rgba(49, 130, 206, 0.1);
        }

        .delete-form {
            display: inline;
        }

        .delete-button {
            background-color: #e53e3e;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            height: 34px;
            white-space: nowrap;
        }

        .delete-button:hover {
            background-color: #c53030;
        }

        .cart-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
            gap: 12px;
        }

        .cart-back-link {
            color: #3182ce;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .cart-back-link:hover {
            text-decoration: underline;
        }

        .btn-checkout {
            background-color: #3182ce;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
            transition: background-color 0.2s;
            font-size: 0.95rem;
            white-space: nowrap;
        }

        .btn-checkout:hover {
            background-color: #2563eb;
        }

        @media (max-width: 768px) {
            .custom-table {
                font-size: 0.85rem;
            }

            .custom-table th,
            .custom-table td {
                padding: 10px 6px;
            }

            .quantity-input {
                width: 45px;
                padding: 4px;
                font-size: 13px;
                height: 32px;
            }

            .delete-button {
                padding: 4px 8px;
                font-size: 11px;
                height: 32px;
            }

            .quantity-form {
                gap: 3px;
            }

            .cart-actions {
                flex-direction: column-reverse;
                gap: 8px;
            }

            .cart-back-link,
            .btn-checkout {
                width: 100%;
                text-align: center;
                padding: 12px 16px;
                font-size: 0.95rem;
            }

            .cart-back-link {
                padding: 10px 16px;
            }
        }

        @media (max-width: 560px) {
            .custom-table th {
                display: none;
            }

            .custom-table,
            .custom-table tbody,
            .custom-table tr {
                display: block;
                width: 100%;
                border: none;
                padding: 0;
                margin: 0;
                background: transparent;
            }

            .custom-table tr {
                margin-bottom: 12px;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                padding: 14px;
                background: #fff;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }

            .custom-table td {
                display: block;
                padding: 0;
                border: none;
                text-align: left;
                margin-bottom: 10px;
            }

            .custom-table td:last-child {
                margin-bottom: 0;
            }

            .custom-table .font-bold {
                font-size: 1rem;
                font-weight: 700;
                color: #1f2937;
                margin-bottom: 6px;
                display: block;
            }

            .custom-table .text-italic {
                font-size: 0.85rem;
                color: #6b7280;
                font-style: italic;
                display: block;
            }

            .quantity-form {
                display: flex;
                gap: 8px;
                align-items: center;
            }

            .quantity-input {
                width: 70px;
                padding: 8px;
                font-size: 14px;
            }

            .delete-form {
                display: block;
                width: 100%;
            }

            .delete-button {
                width: 100%;
                padding: 10px 12px;
                font-size: 13px;
            }

            .cart-actions {
                flex-direction: column-reverse;
                gap: 10px;
            }

            .cart-back-link,
            .btn-checkout {
                width: 100%;
                text-align: center;
                padding: 12px 16px;
            }
        }
    </style>

    <script>
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const form = this.closest('.quantity-form');
                if (form) {
                    form.submit();
                }
            });
        });
    </script>
</x-site-layout>