<?php

namespace App\Http\Controllers;

use App\Models\Bestelling;
use App\Models\Mandje;
use App\Models\Materiaal;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

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

    public function getCount()
    {
        $cart = Mandje::where('gebruiker_id', Auth::id())->first();
        $itemCount = $cart ? $cart->materialen()->count() : 0;

        return response()->json(['count' => $itemCount]);
    }

    public function getSuggestions($materiaalId)
    {
        $materiaal = Materiaal::with('subcategorie')->findOrFail($materiaalId);

        // First try to get suggestions from the same subcategory
        $suggestions = Materiaal::where('materiaal_subcategorie_id', $materiaal->materiaal_subcategorie_id)
            ->where('id', '!=', $materiaalId)
            ->orderByRaw('COALESCE(order_count, 0) DESC')
            ->limit(3)
            ->get();

        // If not enough suggestions from same subcategory, try from same category
        if ($suggestions->count() < 3) {
            $categoryId = $materiaal->subcategorie->materiaal_categorie_id;
            $suggestionIds = $suggestions->pluck('id')->toArray();

            $suggestionsFromCategory = Materiaal::with('subcategorie')
                ->whereHas('subcategorie', function ($query) use ($categoryId) {
                    $query->where('materiaal_categorie_id', $categoryId);
                })
                ->where('id', '!=', $materiaalId)
                ->whereNotIn('id', $suggestionIds)
                ->orderByRaw('COALESCE(order_count, 0) DESC')
                ->limit(3 - $suggestions->count())
                ->get();

            $suggestions = $suggestions->concat($suggestionsFromCategory);
        }

        $suggestions = $suggestions->map(function ($item) {
            return [
                'id' => $item->id,
                'naam' => $item->naam,
                'beschrijving' => $item->beschrijving,
                'foto' => $item->foto,
            ];
        });

        return response()->json($suggestions);
    }

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

    // Herbestel een vorige bestelling
    public function reorder(Bestelling $bestelling)
    {
        if ($bestelling->gebruiker_id !== Auth::id()) {
            abort(403);
        }

        $cart = $this->getOrCreateCart();
        $cart->materialen()->detach();

        foreach ($bestelling->materialen as $materiaal) {
            $cart->materialen()->attach($materiaal->id, ['aantal' => $materiaal->pivot->aantal]);
        }

        return redirect()->route('winkelmandje.checkout')->with('success', 'Bestelling #'.str_pad($bestelling->id, 5, '0', STR_PAD_LEFT).' in mandje geplaatst. Pas aan indien nodig en bevestig.');
    }

    // Toon het overzicht van de bestellingen voor de stockbeheerder
    public function overzicht(Request $request)
    {
        $zoekterm = $request->get('zoekterm');
        $periode = $request->get('periode');

        $bestellingen = Bestelling::whereHas('gebruiker', function ($q) {
            $q->where('role_id', Role::TECHNIEKER);
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

        $todayOrderCount = Bestelling::whereHas('gebruiker', function ($q) {
            $q->where('role_id', Role::TECHNIEKER);
        })->whereDate('created_at', today())->count();

        return view('pages.overzicht', compact('bestellingen', 'zoekterm', 'periode', 'todayOrderCount'));
    }

    // Annuleer een bestelling als stockbeheerder
    public function annuleerBestelling(Bestelling $bestelling)
    {
        $user = Auth::user();

        \Log::info('annuleerBestelling called', [
            'user_id' => $user->id,
            'user_role' => $user->role_id,
            'bestelling_id' => $bestelling->id,
            'gebruiker_id' => $bestelling->gebruiker_id,
        ]);

        if ($user->role_id === Role::TECHNIEKER && $bestelling->gebruiker_id !== $user->id) {
            \Log::warning('annuleerBestelling 403 - technieker owns mismatch', [
                'user_id' => $user->id,
                'gebruiker_id' => $bestelling->gebruiker_id,
            ]);
            abort(403);
        }

        if (! in_array($user->role_id, [Role::STOCKBEHEERDER, Role::ADMIN, Role::TECHNIEKER])) {
            \Log::warning('annuleerBestelling 403 - wrong role', [
                'role' => $user->role_id,
            ]);
            abort(403);
        }

        if ($bestelling->isGeannuleerd()) {
            return redirect()->route('overzicht')->with('error', 'Deze bestelling is al geannuleerd.');
        }

        if ($user->role_id === Role::TECHNIEKER && ! $bestelling->kanNogGeannuleerdWorden()) {
            return redirect()->route('bestellingen')->with('error', 'Deze bestelling kan niet meer geannuleerd worden omdat de gewenste levertijd al verstreken is.');
        }

        $bestelling->annuleer();

        $route = $user->role_id === Role::TECHNIEKER ? 'bestellingen' : 'overzicht';

        return redirect()->route($route)->with('success', 'Bestelling #'.str_pad($bestelling->id, 5, '0', STR_PAD_LEFT).' succesvol geannuleerd.');
    }

    // Toon het formulier voor het bewerken van een bestelling
    public function editOrder($id)
    {
        $bestelling = Bestelling::findOrFail($id);

        // Check authorization using Gate
        if (! Gate::allows('update', $bestelling)) {
            abort(403);
        }

        return view('pages.bewerk-bestelling', compact('bestelling'));
    }

    // Werk een bestaande bestelling bij
    public function updateOrder(Request $request, $id)
    {
        $bestelling = Bestelling::findOrFail($id);

        // Check authorization using Gate
        if (! Gate::allows('update', $bestelling)) {
            abort(403);
        }

        $request->validate([
            'gevraagde_datum' => 'required|date|after_or_equal:today',
            'locatie' => 'nullable|string|max:255',
            'use_custom_location' => 'boolean',
            'opmerking' => 'nullable|string|max:1000',
            'materials' => 'nullable|array',
            'materials.*' => 'integer|min:1|max:10000',
            'removed_materials' => 'nullable|array',
        ]);

        $user = Auth::user();
        $useCustomLocation = $request->input('use_custom_location', false);
        $locatie = $useCustomLocation
            ? $request->input('locatie')
            : ($user->getDepotLocation() ?? $request->input('locatie'));

        if (! $locatie) {
            return back()->withErrors(['locatie' => 'Locatie is vereist.']);
        }

        DB::transaction(function () use ($request, $bestelling, $locatie, $useCustomLocation) {
            // Update order fields
            $bestelling->update([
                'gevraagde_datum' => $request->input('gevraagde_datum'),
                'locatie' => $locatie,
                'opmerking' => $request->input('opmerking'),
                'custom_location_used' => $useCustomLocation,
            ]);

            // Handle material updates
            $materials = $request->input('materials', []);
            $removedMaterials = $request->input('removed_materials', []);

            if (! empty($materials)) {
                // Get currently attached materials before any changes
                $currentMaterialIds = $bestelling->materialen()->pluck('bestelling_materialen.materiaal_id')->toArray();

                // Process each material in the form
                foreach ($materials as $materialId => $quantity) {
                    $quantity = (int) $quantity;

                    if (in_array($materialId, $currentMaterialIds)) {
                        // Material already attached - update its quantity
                        $bestelling->materialen()->updateExistingPivot($materialId, ['aantal' => $quantity]);
                    } else {
                        // New material - attach it
                        $bestelling->materialen()->attach($materialId, ['aantal' => $quantity]);
                    }
                }
            }

            // Remove materials that were deleted
            if (! empty($removedMaterials)) {
                $bestelling->materialen()->detach(array_keys($removedMaterials));
            }
        });

        $bestelling->markAsEdited();

        $route = $user->role_id === Role::TECHNIEKER ? 'bestellingen' : 'overzicht';

        return redirect()->route($route)->with('success', 'Bestelling succesvol bijgewerkt!');
    }
}
