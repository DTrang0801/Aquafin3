<x-site-layout>
    <div style="max-width: 900px; margin: 40px auto; padding: 0 20px;">
        
        <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="color: #000000; font-size: 26px; font-weight: 700; margin: 0 0 4px 0;">Mijn Bestellingen</h1>
                <p style="color: #000000; font-size: 14px; margin: 0;">Overzicht jouw aangevraagde materialen.</p>
            </div>
            <a href="{{ route('materialen') }}" style="background-color: #3b82f6; color: white; text-decoration: none; font-size: 14px; font-weight: 600; padding: 10px 16px; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#2563eb'" onmouseout="this.style.backgroundColor='#3b82f6'">
                + Nieuwe Bestelling
            </a>
        </div>

        @if(session('success'))
            <div style="background-color: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #4ade80; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 500;">
                {{ session('success') }}
            </div>
        @endif

        @if($bestellingen->isEmpty())
            <div style="background-color: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 40px; text-align: center;">
                <p style="color: #94a3b8; font-size: 16px; margin-bottom: 16px;">Je hebt momenteel nog geen bestellingen geplaatst.</p>
                <a href="{{ route('winkelmandje.index') }}" style="color: #3b82f6; text-decoration: underline; font-weight: 500;">Naar mijn winkelmandje →</a>
            </div>
        @else
            @foreach($bestellingen as $bestelling)
                <div style="background-color: #1e293b; border: 1px solid #334155; border-radius: 12px; margin-bottom: 24px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);">
                    
                    <div style="background-color: rgba(51, 65, 85, 0.4); border-bottom: 1px solid #334155; padding: 16px 20px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px;">
                        <div>
                            <span style="color: #94a3b8; font-size: 12px; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Bestelnummer</span>
                            <span style="color: #f1f5f9; font-size: 15px; font-weight: 600;">#{{ str_pad($bestelling->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div>
                            <span style="color: #94a3b8; font-size: 12px; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Geplaatst Op</span>
                            <span style="color: #cbd5e1; font-size: 14px;">{{ $bestelling->created_at->format('d-m-Y H:i') }}</span>
                        </div>
                        <div>
                            <span style="color: #94a3b8; font-size: 12px; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Gewenste Levering</span>
                            <span style="color: #cbd5e1; font-size: 14px; font-weight: 600;">
                                {{ \Carbon\Carbon::parse($bestelling->gevraagde_datum)->format('d-m-Y') }} om {{ \Carbon\Carbon::parse($bestelling->gevraagde_tijd)->format('H:i') }}
                            </span>
                        </div>
                        <div>
                            <span style="color: #94a3b8; font-size: 12px; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Locatie</span>
                            <span style="background-color: #0f172a; border: 1px solid #475569; color: #e2e8f0; font-size: 13px; font-weight: 500; padding: 4px 10px; border-radius: 6px; display: inline-block;">
                                📍 {{ $bestelling->locatie }}
                            </span>
                        </div>
                    </div>

                    <div style="padding: 20px;">
                        @if($bestelling->opmerking)
                            <div style="background-color: #0f172a; border-left: 3px solid #64748b; padding: 10px 14px; border-radius: 0 6px 6px 0; margin-bottom: 16px;">
                                <span style="color: #94a3b8; font-size: 12px; font-weight: 700; display: block; text-transform: uppercase; margin-bottom: 2px;">Opmerking:</span>
                                <p style="color: #cbd5e1; font-size: 13px; margin: 0; font-style: italic;">"{{ $bestelling->opmerking }}"</p>
                            </div>
                        @endif

                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead>
                                <tr style="border-bottom: 1px solid #334155;">
                                    <th style="padding: 8px 0; color: #94a3b8; font-size: 12px; font-weight: 700; text-transform: uppercase;">Artikelomschrijving</th>
                                    <th style="padding: 8px 0; color: #94a3b8; font-size: 12px; font-weight: 700; text-transform: uppercase; text-align: right; width: 120px;">Aantal Besteld</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bestelling->materialen as $materiaal)
                                    <tr style="border-bottom: 1px solid rgba(51, 65, 85, 0.2);">
                                        <td style="padding: 12px 0; color: #f8fafc; font-size: 14px;">
                                            <span style="font-weight: 500;">{{ $materiaal->naam }}</span>
                                            @if($materiaal->belangrijk)
                                                <span style="background-color: rgba(239, 68, 68, 0.15); color: #ef4444; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">⚠️ Kritiek</span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px 0; color: #cbd5e1; font-size: 14px; font-weight: 700; text-align: right;">
                                            {{ $materiaal->pivot->aantal }} stuks
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            @endforeach
        @endif
    </div>
</x-site-layout>