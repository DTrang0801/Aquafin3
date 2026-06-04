<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materiaal;
use App\Models\Bestelling;
use App\Models\Mandje;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CartController extends Controller

{
    // Fetch or instantiate the active user's cart
    private function getOrCreateCart()
    {
        return Mandje::firstOrCreate(['gebruiker_id' => Auth::id()]);
    }

    // Display the Cart Page
    public function index()
    {
        // Haal het mandje op van de ingelogde gebruiker inclusief de materialen
        $cart = Mandje::where('gebruiker_id', Auth::id())->with('materialen.subcategorie')->first();
        
        // Als de gebruiker nog geen mandje heeft, maken we een lege collectie aan
        $materialen = $cart ? $cart->materialen : collect();

        // Stuur de variabele $materialen expliciet door naar de juiste view map (pages/winkelmandje)
        return view('pages.winkelmandje', compact('materialen'));
    }

    // Add item or increment quantity
    public function add(Request $request)
    {
        $request->validate([
            'materiaal_id' => 'required|exists:materialen,id',
            'aantal' => 'required|integer|min:1',
        ]);

        $cart = $this->getOrCreateCart();
        $materiaalId = $request->input('materiaal_id');
        $aantal = $request->input('aantal');

        // Check if item is already present in cart
        $existingItem = $cart->materialen()->where('materiaal_id', $materiaalId)->first();

        if ($existingItem) {
            // Update quantity on existing pivot record
            $newQuantity = $existingItem->pivot->aantal + $aantal;
            $cart->materialen()->updateExistingPivot($materiaalId, ['aantal' => $newQuantity]);
        } else {
            // Attach new item
            $cart->materialen()->attach($materiaalId, ['aantal' => $aantal]);
        }

        return redirect()->back()->with('success', 'Materiaal is toegevoegd aan je mandje!');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['aantal' => 'required|integer|min:1']);
        
        $cart = $this->getOrCreateCart();
        $cart->materialen()->updateExistingPivot($id, ['aantal' => $request->aantal]);

        return redirect()->route('winkelmandje.index')->with('success', 'Winkelmandje bijgewerkt!');
    }

    // Remove item from Cart
    public function destroy($id)
    {
        $cart = $this->getOrCreateCart();
        $cart->materialen()->detach($id);

        return redirect()->route('winkelmandje.index')->with('success', 'Materiaal verwijderd uit winkelmandje.');
    }

    
    public function checkout()
    {
        // Retrieve the current technician's cart and items
        $mandje = Mandje::where('gebruiker_id', Auth::id())->with('materialen')->first();

        // Redirect back to cart if the cart is empty
        if (!$mandje || $mandje->materialen->isEmpty()) {
            return redirect()->route('winkelmandje.index')->with('error', 'Je winkelmandje is leeg!');
        }

        $materialen = $mandje->materialen;

        return view('pages.bestel', compact('materialen'));
    }

    public function confirmOrder(Request $request)
    {
        // Validate required fields based on the Bestelling fillables
        $request->validate([
            'gevraagde_datum' => 'required|date|after_or_equal:today',
            'gevraagde_tijd'  => 'required',
            'locatie'         => 'required|string|max:255',
            'opmerking'       => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();
        $mandje = Mandje::where('gebruiker_id', $userId)->first();

        if (!$mandje || $mandje->materialen->isEmpty()) {
            return redirect()->route('winkelmandje.index')->with('error', 'Er ging iets mis met het verwerken van de bestelling.');
        }

        // Wrap in a database transaction to ensure data safety
        DB::transaction(function () use ($request, $userId, $mandje) {
            // 1. Create the master order entry
            $bestelling = Bestelling::create([
                'gebruiker_id'    => $userId,
                'gevraagde_datum' => $request->input('gevraagde_datum'),
                'gevraagde_tijd'  => $request->input('gevraagde_tijd'),
                'locatie'         => $request->input('locatie'),
                'opmerking'       => $request->input('opmerking'),
            ]);

            // 2. Attach items into the bestelling_materialen pivot table
            foreach ($mandje->materialen as $materiaal) {
                $bestelling->materialen()->attach($materiaal->id, [
                    'aantal' => $materiaal->pivot->aantal
                ]);
            }

            // 3. Completely wipe out items from the cart pivot now that it's ordered
            $mandje->materialen()->detach();
        });

        return redirect()->route('bestellingen')->with('success', 'Bestelling succesvol geplaatst!');
    }
    
    public function indexOrders()
    {
        // Retrieve all orders for the current user, eager loading the nested materials
        $bestellingen = Bestelling::where('gebruiker_id', Auth::id())
            ->with('materialen')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.bestellingen', compact('bestellingen'));
    }
}