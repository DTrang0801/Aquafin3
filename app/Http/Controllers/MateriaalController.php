<?php

namespace App\Http\Controllers;

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

    public function index(Request $request)
    {
        // Haal de belangrijke materialen op
        $belangrijkeMaterialen = Materiaal::where('belangrijk', true)
                                          ->with('subcategorie')
                                          ->get();

        // Haal de hoofdstructuur op voor de lijst
        // $categorieen = Materiaalcategorie::with('subcategorieen.materialen')->get();
        
        // Zoekbalk toevoegen
        $search = $request->input('search');

        $categorieen = Materiaalcategorie::with(['subcategorieen.materialen' => function($query) use ($search) {
        if ($search) {
        $query->where('naam', 'like', '%' . $search . '%');
        }
        }])->get();

        // Stuur beide collecties naar de view
        return view('pages.materialen', compact('belangrijkeMaterialen', 'categorieen'));
    }
}
