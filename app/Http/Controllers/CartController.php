<?php

namespace App\Http\Controllers;

use App\Models\Bestelling;
use App\Models\Mandje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // Haal op of creëer het mandje van de ingelogde gebruiker
    private function getOrCreateCart()
    {
        return Mandje::firstOrCreate(['gebruiker_id' => Auth::id()]);
    }

    public function index()
    {
        // Haal het mandje op van de ingelogde gebruiker
        $cart = Mandje::where('gebruiker_id', Auth::id())->with('materialen.subcategorie')->first();

        // Als de gebruiker nog geen mandje heeft, maken we een lege mandje aan
        $materialen = $cart ? $cart->materialen : collect();

        return view('pages.winkelmandje', compact('materialen'));
    }

    // Voeg een item toe of verhoog de hoeveelheid
    public function add(Request $request)
    {
        $request->validate([
            'materiaal_id' => 'required|exists:materialen,id',
            'aantal' => 'required|integer|min:1',
        ]);

        $cart = $this->getOrCreateCart();
        $materiaalId = $request->input('materiaal_id');
        $aantal = $request->input('aantal');

        // Controleer of het item al in het mandje aanwezig is
        $existingItem = $cart->materialen()->where('materiaal_id', $materiaalId)->first();

        // Als het item al in het mandje aanwezig is, verhoog de hoeveelheid
        if ($existingItem) {
            $newQuantity = $existingItem->pivot->aantal + $aantal;
            $cart->materialen()->updateExistingPivot($materiaalId, ['aantal' => $newQuantity]);
        } else {
            // Voeg het nieuwe item toe aan het mandje
            $cart->materialen()->attach($materiaalId, ['aantal' => $aantal]);
        }

        return redirect()->back()->with('success', 'Materiaal is toegevoegd aan je mandje!');
    }

    // Update de hoeveelheid van een item in het mandje
    public function update(Request $request, $id)
    {
        $request->validate(['aantal' => 'required|integer|min:1']);

        $cart = $this->getOrCreateCart();
        $cart->materialen()->updateExistingPivot($id, ['aantal' => $request->aantal]);

        return redirect()->route('winkelmandje.index')->with('success', 'Winkelmandje bijgewerkt!');
    }

    // Verwijder een item uit het mandje
    public function destroy($id)
    {
        $cart = $this->getOrCreateCart();
        $cart->materialen()->detach($id);

        return redirect()->route('winkelmandje.index')->with('success', 'Materiaal verwijderd uit winkelmandje.');
    }

    // Toon de bestelpagina voor de ingelogde gebruiker
    public function checkout()
    {
        $mandje = Mandje::where('gebruiker_id', Auth::id())->with('materialen')->first();

        if (! $mandje || $mandje->materialen->isEmpty()) {
            return redirect()->route('winkelmandje.index')->with('error', 'Je winkelmandje is leeg!');
        }

        $materialen = $mandje->materialen;
        $user = Auth::user();

        return view('pages.bestel', compact('materialen', 'user'));
    }

    // Bevestig de bestelling
    public function confirmOrder(Request $request)
    {
        $request->validate([
            'gevraagde_datum' => 'required|date|after_or_equal:today',
            'locatie' => 'nullable|string|max:255',
            'use_custom_location' => 'boolean',
            'opmerking' => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();
        $user = Auth::user();
        $mandje = Mandje::where('gebruiker_id', $userId)->first();

        if (! $mandje || $mandje->materialen->isEmpty()) {
            return redirect()->route('winkelmandje.index')->with('error', 'Er ging iets mis met het verwerken van de bestelling.');
        }

        // Controleer of de gebruiker een aangepaste locatie heeft geselecteerd
        $useCustomLocation = $request->input('use_custom_location', false);
        $locatie = $useCustomLocation
            ? $request->input('locatie')
            : ($user->getDepotLocation() ?? $request->input('locatie'));

        if (! $locatie) {
            return back()->withErrors(['locatie' => 'Locatie is vereist.']);
        }

        DB::transaction(function () use ($request, $userId, $mandje, $locatie, $useCustomLocation) {
            $bestelling = Bestelling::create([
                'gebruiker_id' => $userId,
                'gevraagde_datum' => $request->input('gevraagde_datum'),
                'locatie' => $locatie,
                'opmerking' => $request->input('opmerking'),
                'custom_location_used' => $useCustomLocation,
            ]);

            foreach ($mandje->materialen as $materiaal) {
                $aantal = $materiaal->pivot->aantal;
                $bestelling->materialen()->attach($materiaal->id, [
                    'aantal' => $aantal,
                ]);

                $materiaal->increment('order_count', $aantal);
            }

            $mandje->materialen()->detach();
        });

        return redirect()->route('bestellingen')->with('success', 'Bestelling succesvol geplaatst!');
    }

    // Toon de bestelgeschiedenis van de ingelogde gebruiker
    public function indexOrders(Request $request)
    {
        $zoekterm = $request->get('zoekterm');
        $periode = $request->get('periode');

        $bestellingen = Bestelling::where('gebruiker_id', Auth::id())
            ->with('materialen')
            ->when($zoekterm, function ($query, $zoekterm) {
                $query->where(function ($q) use ($zoekterm) {
                    $q->where('id', 'like', "%$zoekterm%")
                        ->orWhere('locatie', 'like', "%$zoekterm%")
                        ->orWhere('opmerking', 'like', "%$zoekterm%")
                        ->orWhereHas('materialen', function ($mq) use ($zoekterm) {
                            $mq->where('naam', 'like', "%$zoekterm%");
                        });
                });
            })
            ->when($periode, function ($query, $periode) {
                $now = now();
                $start = match ($periode) {
                    'vandaag' => $now->copy()->startOfDay(),
                    'week' => $now->copy()->startOfWeek(),
                    'maand' => $now->copy()->startOfMonth(),
                    '3maanden' => $now->copy()->subMonths(3)->startOfDay(),
                    default => null,
                };
                if ($start) {
                    $query->where('created_at', '>=', $start);
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.bestellingen', compact('bestellingen', 'zoekterm', 'periode'));
    }

    // Toon het overzicht van de bestellingen voor de stockbeheerder
    public function overzicht(Request $request)
    {
        $zoekterm = $request->get('zoekterm');
        $periode = $request->get('periode');

        $bestellingen = Bestelling::whereHas('gebruiker', function ($q) {
            $q->where('role', 'technieker');
        })->with('gebruiker', 'materialen')
            ->when($zoekterm, function ($query, $zoekterm) {
                $query->where(function ($q) use ($zoekterm) {
                    $q->where('id', 'like', "%$zoekterm%")
                        ->orWhere('locatie', 'like', "%$zoekterm%")
                        ->orWhere('opmerking', 'like', "%$zoekterm%")
                        ->orWhereHas('gebruiker', function ($uq) use ($zoekterm) {
                            $uq->where('name', 'like', "%$zoekterm%");
                        })
                        ->orWhereHas('materialen', function ($mq) use ($zoekterm) {
                            $mq->where('naam', 'like', "%$zoekterm%");
                        });
                });
            })
            ->when($periode, function ($query, $periode) {
                $now = now();
                $start = match ($periode) {
                    'vandaag' => $now->copy()->startOfDay(),
                    'week' => $now->copy()->startOfWeek(),
                    'maand' => $now->copy()->startOfMonth(),
                    '3maanden' => $now->copy()->subMonths(3)->startOfDay(),
                    default => null,
                };
                if ($start) {
                    $query->where('created_at', '>=', $start);
                }
            })
            ->orderBy('created_at', 'desc')->get();

        return view('pages.overzicht', compact('bestellingen', 'zoekterm', 'periode'));
    }
}
