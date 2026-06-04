<x-site-layout>
    <div style="max-width: 800px; margin: 40px auto; padding: 0 20px;">
        
        <a href="{{ route('winkelmandje.index') }}" style="color: #94a3b8; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 20px; transition: color 0.2s;" onmouseover="this.style.color='#f1f5f9'" onmouseout="this.style.color='#94a3b8'">
            ← Terug naar winkelmandje
        </a>

        <div style="background-color: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 30px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);">
            <h1 style="color: #f1f5f9; font-size: 24px; font-weight: 700; margin-bottom: 6px;">Bestelling Afronden</h1>
            <p style="color: #94a3b8; font-size: 14px; margin-bottom: 24px;">Controleer je materialen en vul de vereiste leveringsdetails in.</p>

            @if ($errors->any())
                <div style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('winkelmandje.confirm') }}" method="POST">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                    
                    <div style="grid-column: span 2;">
                        <label for="locatie" style="display: block; color: #cbd5e1; font-size: 14px; font-weight: 600; margin-bottom: 6px;">Leverlocatie / Werf *</label>
                        <input type="text" name="locatie" id="locatie" value="{{ old('locatie') }}" placeholder="Bijv. Werf Antwerpen Knooppunt Noord of Magazijn B" style="width: 100%; height: 42px; background-color: #0f172a; border: 1px solid #475569; border-radius: 6px; padding: 0 14px; color: white; font-size: 14px;" required>
                    </div>

                    <div>
                        <label for="gevraagde_datum" style="display: block; color: #cbd5e1; font-size: 14px; font-weight: 600; margin-bottom: 6px;">Gevraagde Datum *</label>
                        <input type="date" name="gevraagde_datum" id="gevraagde_datum" value="{{ old('gevraagde_datum', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" style="width: 100%; height: 42px; background-color: #0f172a; border: 1px solid #475569; border-radius: 6px; padding: 0 14px; color: white; font-size: 14px;" required>
                    </div>

                    <div>
                        <label for="gevraagde_tijd" style="display: block; color: #cbd5e1; font-size: 14px; font-weight: 600; margin-bottom: 6px;">Gewenst Tijdstip *</label>
                        <input type="time" name="gevraagde_tijd" id="gevraagde_tijd" value="{{ old('gevraagde_tijd', '08:00') }}" style="width: 100%; height: 42px; background-color: #0f172a; border: 1px solid #475569; border-radius: 6px; padding: 0 14px; color: white; font-size: 14px;" required>
                    </div>

                    <div style="grid-column: span 2;">
                        <label for="opmerking" style="display: block; color: #cbd5e1; font-size: 14px; font-weight: 600; margin-bottom: 6px;">Algemene Opmerking / Instructies</label>
                        <textarea name="opmerking" id="opmerking" rows="3" placeholder="Voeg eventueel extra opmerkingen toe voor de stockbeheerder..." style="width: 100%; background-color: #0f172a; border: 1px solid #475569; border-radius: 6px; padding: 12px 14px; color: white; font-size: 14px; font-family: inherit; resize: vertical;">{{ old('opmerking') }}</textarea>
                    </div>

                </div>

                <h3 style="color: #cbd5e1; font-size: 16px; font-weight: 600; margin-bottom: 12px; border-bottom: 1px solid #334155; padding-bottom: 6px;">Overzicht Items</h3>
                <div style="background-color: #0f172a; border-radius: 8px; border: 1px solid #334155; overflow: hidden; margin-bottom: 25px;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="background-color: rgba(51, 65, 85, 0.3); border-bottom: 1px solid #334155;">
                                <th style="padding: 10px 16px; color: #94a3b8; font-size: 12px; font-weight: 700; text-transform: uppercase;">Materiaal</th>
                                <th style="padding: 10px 16px; color: #94a3b8; font-size: 12px; font-weight: 700; text-transform: uppercase; text-align: right; width: 100px;">Aantal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materialen as $item)
                                <tr style="border-bottom: 1px solid #1e293b;">
                                    <td style="padding: 12px 16px; color: #f8fafc; font-size: 14px;">
                                        <span style="font-weight: 600;">{{ $item->naam }}</span>
                                        @if($item->belangrijk)
                                            <span style="background-color: rgba(239, 68, 68, 0.15); color: #ef4444; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">⚠️ Kritiek</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 16px; color: #cbd5e1; font-size: 14px; font-weight: 700; text-align: right;">
                                        {{ $item->pivot->aantal }}x
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: flex-end; align-items: center; gap: 12px;">
                    <a href="{{ route('winkelmandje.index') }}" style="color: #cbd5e1; text-decoration: none; font-size: 14px; font-weight: 500; padding: 10px 18px; border: 1px solid #475569; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#334155'" onmouseout="this.style.backgroundColor='transparent'">
                        Annuleren
                    </a>
                    <button type="submit" style="background-color: #22c55e; color: white; font-size: 14px; font-weight: 700; padding: 10px 24px; border: none; border-radius: 6px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#16a34a'" onmouseout="this.style.backgroundColor='#22c55e'">
                        ✓ Bestelling Bevestigen
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-site-layout>