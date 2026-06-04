<?php

namespace App\Http\Controllers;

use App\Models\Materiaal;
use App\Models\Materiaalcategorie;
use App\Models\MateriaalSubcategorie;
use Illuminate\Http\Request;

class MateriaalController extends Controller
{
    // Materiaal in een tabel tonen

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

        $categorieen = Materiaalcategorie::with(['subcategorieen.materialen' => function ($query) use ($search) {
            if ($search) {
                $query->where('naam', 'like', '%'.$search.'%');
            }
        }])->get();

        // Stuur beide collecties naar de view
        return view('pages.materialen', compact('belangrijkeMaterialen', 'categorieen'));
    }

    public function create()
    {
        $subcategorieen = MateriaalSubcategorie::all();

        return view('pages.materialen-create', compact('subcategorieen'));
    }

    public function store(Request $request)
    {
        $data = [
            'naam' => $request->naam,
            'beschrijving' => $request->beschrijving,
            'materiaal_subcategorie_id' => $request->materiaal_subcategorie_id,
            'belangrijk' => $request->has('belangrijk'),
        ];

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('materialen', 'public');
        }

        Materiaal::create($data);

        return redirect('/materialen');
    }
}
