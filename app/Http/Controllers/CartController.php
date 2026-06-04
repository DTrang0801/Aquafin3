<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materiaal;
use App\Models\Mandje;
use Illuminate\Support\Facades\Auth;

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
}