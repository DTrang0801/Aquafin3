<x-site-layout>
   <!-- <div class="winkelmandje-container">
        <h1 class="winkelmandje-title">Winkelmandje</h1>
        <p class="winkelmandje-subtitle">Hier zie je een klein overzicht van de gekozen materialen.</p>

        <div class="winkelmandje-layout">
            <div class="producten-lijst">

                <div class="product-kaart">
                    <div class="product-info">
                        <h2>Bouten M6</h2>
                    </div>
                    <div class="product-details">
                        <span>Aantal: 2</span>
                    </div>
                </div>

                <div class="product-kaart">
                    <div class="product-info">
                        <h2>Borgmoeren</h2>
                    </div>
                    <div class="product-details">
                        <span>Aantal: 1</span>
                    </div>
                </div>

                <div class="product-kaart">
                    <div class="product-info">
                        <h2>Helmen</h2>
                    </div>
                    <div class="product-details">
                        <span>Aantal: 3</span>
                    </div>
                </div>
            </div>

            <div class="samenvatting-kaart">
                <h2>Overzicht</h2>
                <div class="samenvatting-regel">
                    <span>Aantal producten</span>
                    <span>6</span>
                </div>
                <div class="samenvatting-regel">
                    <span>Verzendkosten</span>
                </div>
                <div class="samenvatting-regel totaal">
                </div>

                <button class="bestel-btn">Ga verder met bestellen</button>
            </div>
        </div>
    </div> -->

    @if($materialen->isEmpty())
        <p>Je mandje is leeg.</p>
        @else

        @foreach($materialen as $materiaal)
            <div class="product-kaart">
                <div class="product-info">
                <h2>{{ $materiaal->naam }}</h2>
            </div>

            <div class="product-details">
                <span>Aantal: {{ $materiaal->pivot->aantal }}</span>
                </div>
            </div>
        @endforeach
    @endif

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