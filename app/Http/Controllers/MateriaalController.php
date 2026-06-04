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

    public function index(Request $request)
    {
        $belangrijkeMaterialen = Materiaal::where('belangrijk', true)
                                          ->with('subcategorie')
                                          ->get();

        $search = $request->input('search');

        $categorieen = Materiaalcategorie::with('subcategorieen.materialen')->get();

        $openCategoryIds = collect();
        $openSubcategoryIds = collect();

        if ($search) {
            foreach ($categorieen as $cat) {
                $catMatch = mb_stripos($cat->naam, $search) !== false;

                foreach ($cat->subcategorieen as $sub) {
                    $subMatch = mb_stripos($sub->naam, $search) !== false;

                    if ($subMatch) {
                        $openSubcategoryIds->push($sub->id);
                    } else {
                        $sub->setRelation('materialen', $sub->materialen->filter(function ($m) use ($search) {
                            return mb_stripos($m->naam, $search) !== false;
                        }));

                        if ($sub->materialen->isNotEmpty()) {
                            $openSubcategoryIds->push($sub->id);
                        }
                    }
                }

                $catSubIds = $cat->subcategorieen->pluck('id');
                if ($catMatch || $openSubcategoryIds->intersect($catSubIds)->isNotEmpty()) {
                    $openCategoryIds->push($cat->id);
                }
            }
        } else {
            $openCategoryIds = $categorieen->pluck('id');
            foreach ($categorieen as $cat) {
                foreach ($cat->subcategorieen as $sub) {
                    $openSubcategoryIds->push($sub->id);
                }
            }
        }

        return view('pages.materialen', compact('belangrijkeMaterialen', 'categorieen', 'openCategoryIds', 'openSubcategoryIds'));
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
}
