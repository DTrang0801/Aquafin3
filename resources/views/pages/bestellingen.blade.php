<x-site-layout>
    <div class="bestelling-container">
        <h1 class="bestelling-title">Bestelling overzicht</h1>
<!--
        <div class="bestelling-layout">
            <div class="bestelling-links">

                <div class="info-kaart">
                    <h2>Klantgegevens</h2>
                    <p><strong>Naam:</strong>Technieker1</p>
                    <p><strong>E-mail:</strong> technieker1@aquafin.test</p>
                    <p><strong>Telefoon:</strong> +32 470 00 00 00</p>
                </div>

                <div class="info-kaart">
                    <h2>Leveradres</h2>
                    <p>Stationsstraat 12</p>
                    <p>1000 Brussel</p>
                    <p>België</p>
                </div>

                <div class="info-kaart">
                    <h2>Bestelde materialen</h2>

                    <div class="bestel-item">
                        <div>
                            <h3>Draadstangen M6</h3>
                            <p>Aantal: 2</p>
                        </div>
                    </div>

                    <div class="bestel-item">
                        <div>
                            <h3>Inslagmoeren</h3>
                            <p>Aantal: 1</p>
                        </div>
                    </div>

                    <div class="bestel-item">
                        <div>
                            <h3>Bouten M6</h3>
                            <p>Aantal: 3</p>
                        </div>
                    </div>
                </div>
            </div>
                                    <div class="bestelling-rechts">
                                            <div class="samenvatting-kaart">
                                                <h2>Samenvatting</h2>

                                                <div class="samenvatting-regel">
                                                    <span>Aantal producten</span>
                                                    <span>6</span>
                                                </div>

                                                <button class="bevestig-btn">Bestelling bevestigen</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
    <style>
        .bestelling-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 20px 60px;
            color: #1f2937;
        }

        .bestelling-title {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .bestelling-subtitle {
            margin-bottom: 30px;
            color: #4b5563;
        }

        .bestelling-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            align-items: start;
        }

        .bestelling-links {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .info-kaart,
        .samenvatting-kaart {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .info-kaart h2,
        .samenvatting-kaart h2 {
            margin-bottom: 16px;
            font-size: 1.3rem;
            color: #111827;
        }

        .info-kaart p {
            margin-bottom: 8px;
            color: #374151;
        }

        .bestel-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .bestel-item:last-child {
            border-bottom: none;
        }

        .bestel-item h3 {
            margin-bottom: 6px;
            font-size: 1.05rem;
        }

        .bestel-item p {
            margin: 0;
            color: #6b7280;
        }

        .bestel-item span {
            font-weight: 700;
            color: #111827;
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

        .bevestig-btn {
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

        .bevestig-btn:hover {
            background-color: #1d4ed8;
        }

      @media (max-width: 768px) {
            .bestelling-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
-->

        @if($bestellingen->isEmpty())
            <p>Geen bestellingen gevonden.</p>
        @else
            <table border="1" cellpadding="8" style="width: 100%; margin-top: 16px">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Technieker</th>
                        <th>Datum</th>
                        <th>Tijd</th>
                        <th>Locatie</th>
                        <th>Materialen</th>
                        <th>Opmerking</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bestellingen as $bestelling)
                        <tr>
                            <td>{{ $bestelling->id }}</td>
                            <td>{{ $bestelling->gebruiker->name }}</td>
                            <td>{{ $bestelling->gevraagde_datum ?? '—' }}</td>
                            <td>{{ $bestelling->gevraagde_tijd ?? '—' }}</td>
                            <td>{{ $bestelling->locatie ?? '—' }}</td>
                            <td>
                                @forelse($bestelling->materialen as $materiaal)
                                    {{ $materiaal->naam }} ({{ $materiaal->pivot->aantal }}x)<br>
                                @empty
                                    —
                                @endforelse
                            </td>
                            <td>{{ $bestelling->opmerking ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</x-site-layout>