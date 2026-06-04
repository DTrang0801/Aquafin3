<?php

namespace App\Http\Controllers;

use App\Models\Mandje;
use Illuminate\Support\Facades\Auth;

class MandjeController extends Controller
{
    public function index()
    {
        $mandje = Mandje::where('gebruiker_id', Auth::id())->first();
        $materialen = $mandje ? $mandje->materialen : collect();
        return view('pages.winkelmandje', compact('materialen'));
    }

    public function toevoegen($materiaalId)
{
    $mandje = Mandje::firstOrCreate(['gebruiker_id' => Auth::id()]);

    if ($mandje->materialen->contains($materiaalId)) {
        $mandje->materialen()->updateExistingPivot($materiaalId, [
            'aantal' => $mandje->materialen->find($materiaalId)->pivot->aantal + 1
        ]);
    } else {
        $mandje->materialen()->attach($materiaalId, ['aantal' => 1]);
    }

    return redirect()->back()->with('success', 'Toegevoegd aan mandje!');
}

public function verwijderen($materiaalId)
{
    $mandje = Mandje::where('gebruiker_id', Auth::id())->first();
    $mandje->materialen()->detach($materiaalId);
    return redirect()->back()->with('success', 'Verwijderd uit mandje!');
}

public function verhogen($materiaalId)
{
    $mandje = Mandje::where('gebruiker_id', Auth::id())->first();
    $mandje->materialen()->updateExistingPivot($materiaalId, [
        'aantal' => $mandje->materialen->find($materiaalId)->pivot->aantal + 1
    ]);
    return redirect()->back();
}

public function verlagen($materiaalId)
{
    $mandje = Mandje::where('gebruiker_id', Auth::id())->first();
    $huidigAantal = $mandje->materialen->find($materiaalId)->pivot->aantal;
    
    if ($huidigAantal <= 1) {
        $mandje->materialen()->detach($materiaalId);
    } else {
        $mandje->materialen()->updateExistingPivot($materiaalId, ['aantal' => $huidigAantal - 1]);
    }
    return redirect()->back();
}
}