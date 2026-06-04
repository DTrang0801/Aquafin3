<?php

namespace App\Http\Controllers;

use App\Models\Bestelling;

class BestellingController extends Controller
{
    public function index()
    {
        $bestellingen = Bestelling::with(['gebruiker', 'materialen'])
            ->orderBy('gevraagde_datum', 'asc')
            ->get();

        return view('pages.bestellingen', [
            'bestellingen' => $bestellingen,
        ]);
    }
}
