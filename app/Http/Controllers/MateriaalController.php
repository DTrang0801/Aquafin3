<?php

namespace App\Http\Controllers;

use App\Models\MateriaalSubcategorie;
use App\Models\Materiaal;
use App\Models\Bestelling;
use App\Models\Mandje;
use App\Models\Materiaalcategorie;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MateriaalController extends Controller
{

    //Materiaal in een tabel tonen

    public function index()
    {
        // Haal de belangrijke materialen op
        $belangrijkeMaterialen = Materiaal::where('belangrijk', true)
                                          ->with('subcategorie')
                                          ->get();

        // Haal de hoofdstructuur op voor de lijst
        $categorieen = Materiaalcategorie::with('subcategorieen.materialen')->get();

        //Stuur beide collecties naar de view
        return view('pages.materialen', compact('belangrijkeMaterialen', 'categorieen'));
    }

        public function create()
    {
        $subcategorieen = MateriaalSubcategorie::all();

        return view('pages.materialen-create', compact('subcategorieen'));
    }
        
        public function store(Request $request)
    {
        Materiaal::create([
            'naam' => $request->naam,
            'beschrijving' => $request->beschrijving,
            'materiaal_subcategorie_id' => $request->materiaal_subcategorie_id,
            'belangrijk' => $request->has('belangrijk'),
        ]);

        return redirect('/materialen');  
    } 
        // Toon beheerpagina met alle materialen voor de stockbeheerder
        public function beheer()
    {
        $materialen = Materiaal::with('subcategorie')->get();

        return view('pages.materialen-beheer', compact('materialen'));
    } 

        public function destroy(Materiaal $materiaal)
    {
        if (Auth::user()->role !== 'stockbeheerder') {
            abort(403);
        }

        $materiaal->delete();

        return redirect()->route('materialen.beheer');
    }

        public function edit(Materiaal $materiaal)
    {
        $subcategorieen = MateriaalSubcategorie::all();

        return view('pages.materialen-edit', compact('materiaal', 'subcategorieen'));
    }

    public function update(Request $request, Materiaal $materiaal)
    {
        $materiaal->update([
            'naam' => $request->naam,
            'beschrijving' => $request->beschrijving,
            'materiaal_subcategorie_id' => $request->materiaal_subcategorie_id,
            'belangrijk' => $request->has('belangrijk'),
        ]);

        return redirect()->route('materialen.beheer');
    }

}
