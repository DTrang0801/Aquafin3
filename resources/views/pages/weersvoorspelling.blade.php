<x-site-layout>
    <div class="risico-container">
        <h1 class="risico-title">Overstromingsrisico komende 5 jaar</h1>
        <p class="risico-subtitle">
            Overzicht op basis van historische neerslaggegevens sinds 2004 en seizoensdrempels voor overstromingsgevaar.
        </p>

        <div class="jaar-grid">
            <div class="jaar-kaart">
                <h2>2026</h2>
                <div class="seizoen risico-hoog">
                    <h3>Winter</h3>
                    <p>Verwachte seizoensneerslag: 308 mm</p>
                    <p>Risico: Hoog</p>
                </div>
                <div class="seizoen risico-laag">
                    <h3>Lente</h3>
                    <p>Verwachte seizoensneerslag: 198 mm</p>
                    <p>Risico: Laag</p>
                </div>
                <div class="seizoen risico-matig">
                    <h3>Zomer</h3>
                    <p>Verwachte seizoensneerslag: 257 mm</p>
                    <p>Risico: Matig</p>
                </div>
                <div class="seizoen risico-laag">
                    <h3>Herfst</h3>
                    <p>Verwachte seizoensneerslag: 262 mm</p>
                    <p>Risico: Laag</p>
                </div>
            </div>

            <div class="jaar-kaart">
                <h2>2027</h2>
                <div class="seizoen risico-hoog">
                    <h3>Winter</h3>
                    <p>Verwachte seizoensneerslag: 310 mm</p>
                    <p>Risico: Hoog</p>
                </div>
                <div class="seizoen risico-laag">
                    <h3>Lente</h3>
                    <p>Verwachte seizoensneerslag: 200 mm</p>
                    <p>Risico: Laag</p>
                </div>
                <div class="seizoen risico-matig">
                    <h3>Zomer</h3>
                    <p>Verwachte seizoensneerslag: 259 mm</p>
                    <p>Risico: Matig</p>
                </div>
                <div class="seizoen risico-laag">
                    <h3>Herfst</h3>
                    <p>Verwachte seizoensneerslag: 264 mm</p>
                    <p>Risico: Laag</p>
                </div>
            </div>

            <div class="jaar-kaart">
                <h2>2028</h2>
                <div class="seizoen risico-hoog">
                    <h3>Winter</h3>
                    <p>Verwachte seizoensneerslag: 312 mm</p>
                    <p>Risico: Hoog</p>
                </div>
                <div class="seizoen risico-laag">
                    <h3>Lente</h3>
                    <p>Verwachte seizoensneerslag: 202 mm</p>
                    <p>Risico: Laag</p>
                </div>
                <div class="seizoen risico-hoog">
                    <h3>Zomer</h3>
                    <p>Verwachte seizoensneerslag: 261 mm</p>
                    <p>Risico: Hoog</p>
                </div>
                <div class="seizoen risico-laag">
                    <h3>Herfst</h3>
                    <p>Verwachte seizoensneerslag: 266 mm</p>
                    <p>Risico: Laag</p>
                </div>
            </div>

            <div class="jaar-kaart">
                <h2>2029</h2>
                <div class="seizoen risico-hoog">
                    <h3>Winter</h3>
                    <p>Verwachte seizoensneerslag: 314 mm</p>
                    <p>Risico: Hoog</p>
                </div>
                <div class="seizoen risico-laag">
                    <h3>Lente</h3>
                    <p>Verwachte seizoensneerslag: 204 mm</p>
                    <p>Risico: Laag</p>
                </div>
                <div class="seizoen risico-hoog">
                    <h3>Zomer</h3>
                    <p>Verwachte seizoensneerslag: 263 mm</p>
                    <p>Risico: Hoog</p>
                </div>
                <div class="seizoen risico-laag">
                    <h3>Herfst</h3>
                    <p>Verwachte seizoensneerslag: 268 mm</p>
                    <p>Risico: Laag</p>
                </div>
            </div>

            <div class="jaar-kaart">
                <h2>2030</h2>
                <div class="seizoen risico-hoog">
                    <h3>Winter</h3>
                    <p>Verwachte seizoensneerslag: 316 mm</p>
                    <p>Risico: Hoog</p>
                </div>
                <div class="seizoen risico-laag">
                    <h3>Lente</h3>
                    <p>Verwachte seizoensneerslag: 206 mm</p>
                    <p>Risico: Laag</p>
                </div>
                <div class="seizoen risico-hoog">
                    <h3>Zomer</h3>
                    <p>Verwachte seizoensneerslag: 265 mm</p>
                    <p>Risico: Hoog</p>
                </div>
                <div class="seizoen risico-laag">
                    <h3>Herfst</h3>
                    <p>Verwachte seizoensneerslag: 270 mm</p>
                    <p>Risico: Laag</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .risico-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px 60px;
            color: #1f2937;
        }

        .risico-title {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .risico-subtitle {
            margin-bottom: 30px;
            color: #4b5563;
            max-width: 850px;
        }

        .jaar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .jaar-kaart {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .jaar-kaart h2 {
            margin-bottom: 16px;
            color: #111827;
        }

        .seizoen {
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 14px;
        }

        .seizoen h3 {
            margin-bottom: 8px;
        }

        .seizoen p {
            margin: 4px 0;
        }

        .risico-laag {
            background-color: #dcfce7;
            color: #166534;
        }

        .risico-matig {
            background-color: #fef3c7;
            color: #92400e;
        }

        .risico-hoog {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</x-site-layout>