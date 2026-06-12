<?php

namespace App\Http\Controllers;

use App\Models\Materiaal;
use App\Models\Materiaalcategorie;
use App\Models\MateriaalSubcategorie;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MateriaalController extends Controller
{
    // Materiaal in een tabel tonen
    public function index(Request $request)
    {
        // Belangrijke materialen ophalen
        $belangrijkeMaterialen = Materiaal::where('belangrijk', true)->with('subcategorie')->get();

        // Zoekopdracht ophalen
        $search = $request->input('search');
        $selectedCatId = $request->input('category_id');
        $selectedSubcatId = $request->input('subcategory_id');

        // Categorieën en subcategorieën ophalen
        $filterCategories = Materiaalcategorie::orderBy('naam', 'asc')->get();
        $filterSubcategories = collect();
        if ($selectedCatId) {
            $filterSubcategories = MateriaalSubcategorie::where('materiaal_categorie_id', $selectedCatId)
                ->orderBy('naam', 'asc')
                ->get();
        }

        // Relatie query ophalen en filteren op basis van de geselecteerde subcategorie
        $query = Materiaalcategorie::with(['subcategorieen' => function ($q) use ($selectedSubcatId) {
            if ($selectedSubcatId) {
                $q->where('id', $selectedSubcatId);
            }
        }, 'subcategorieen.materialen']);

        if ($selectedCatId) {
            $query->where('id', $selectedCatId);
        }

        $rawCategories = $query->get();

        // Ophalen van de open categorieën en subcategorieën
        $openCategoryIds = collect();
        $openSubcategoryIds = collect();

        // Filter de categorieën en subcategorieën op basis van de zoekopdracht
        $categorieen = $rawCategories->filter(function ($cat) use ($search, $openCategoryIds, $openSubcategoryIds) {
            $catMatch = $search ? $this->isTypoTolerantMatch($cat->naam, $search) : true;

            // Filter de subcategorieën binnen deze categorie
            $cat->setRelation('subcategorieen', $cat->subcategorieen->filter(function ($sub) use ($search, $catMatch, $openSubcategoryIds) {
                $subMatch = $search ? $this->isTypoTolerantMatch($sub->naam, $search) : true;

                // Filter de materialen binnen deze subcategorie
                if (! $subMatch && ! $catMatch && $search) {
                    $sub->setRelation('materialen', $sub->materialen->filter(function ($m) use ($search) {
                        return $this->isTypoTolerantMatch($m->naam, $search) ||
                               $this->isTypoTolerantMatch($m->beschrijving, $search);
                    }));
                }

                // Verberg subcategorie volledig als deze geen matching items bevat
                if ($sub->materialen->isEmpty()) {
                    return false;
                }

                // Open de subcategorie alleen bij suggestie-klik
                if (request('suggestie') && $search) {
                    $openSubcategoryIds->push($sub->id);
                }

                return true;
            }));

            // Verberg categorie volledig als deze geen matching items of subcategorieën bevat
            if ($cat->subcategorieen->isEmpty()) {
                return false;
            }

            // Open de categorie alleen bij suggestie-klik
            if (request('suggestie') && ($search || request('category_id') || request('subcategory_id'))) {
                $openCategoryIds->push($cat->id);
            }

            return true;
        });


        return view('pages.materialen', compact(
            'belangrijkeMaterialen',
            'categorieen',
            'openCategoryIds',
            'openSubcategoryIds',
            'filterCategories',
            'filterSubcategories'
        ));
    }

    /**
     * Typo-tolerant matching mechanisme gebaseerd op Levenshtein afstand berekeningen.
     */
    private function isTypoTolerantMatch(?string $haystack, string $needle): bool
    {
        if (empty($haystack)) {
            return false;
        }

        $haystack = mb_strtolower(trim($haystack));
        $needle = mb_strtolower(trim($needle));

        // Exact substring match check
        if (str_contains($haystack, $needle)) {
            return true;
        }

        // Typo tolerance evaluation for multi-word configurations
        $words = explode(' ', $haystack);
        foreach ($words as $word) {
            // Calculate structural character distance deviations
            $distance = levenshtein($word, $needle);

            // Define allowable typo allowances based on length of input query
            $maxAllowedTypos = strlen($needle) > 4 ? 2 : 1;

            if ($distance <= $maxAllowedTypos) {
                return true;
            }
        }

        return false;
    }

    // Suggesties ophalen voor de zoekopdracht
    public function suggesties(Request $request)
    {
        $query = $request->input('q');

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $likePattern = strlen($query) === 1 ? $query.'%' : '%'.$query.'%';

        $exact = Materiaal::where('naam', 'like', $likePattern)
            ->limit(100)
            ->get(['id', 'naam', 'materiaal_subcategorie_id'])
            ->load('subcategorie');

        $exactIds = $exact->pluck('id');

        $typo = collect();
        if (strlen($query) > 1) {
            $typoQuery = $exactIds->isNotEmpty()
                ? Materiaal::whereNotIn('id', $exactIds)
                : Materiaal::query();

            $typo = $typoQuery
                ->get(['id', 'naam', 'materiaal_subcategorie_id'])
                ->filter(fn($m) => $this->isTypoTolerantMatch($m->naam, $query))
                ->load('subcategorie');
        }

        $materialen = $exact->concat($typo)->take(100)->values();

        return response()->json($materialen);
    }

    // Toon creatiepagina voor materialen
    public function create()
    {
        $categorieen = Materiaalcategorie::orderBy('naam')->get();
        $subcategorieen = MateriaalSubcategorie::all();

        return view('pages.materialen-create', compact('categorieen', 'subcategorieen'));
    }

    // Materialen opslaan
    public function store(Request $request)
    {
        $fotoPad = null;

        if ($request->hasFile('foto')) {
            $fotoPad = $request->file('foto')->store('materialen', 'public');
        }

        Materiaal::create([
            'naam' => $request->naam,
            'beschrijving' => $request->beschrijving,
            'materiaal_subcategorie_id' => $request->materiaal_subcategorie_id,
            'belangrijk' => $request->has('belangrijk'),
            'foto' => $fotoPad,
        ]);

        return redirect('/materialen');
    }

    // Toon beheerpagina met alle materialen voor de stockbeheerder
    public function beheer()
    {
        $materialen = Materiaal::with('subcategorie.categorie')->get();

        return view('pages.materialen-beheer', compact('materialen'));
    }

    // Materialen verwijderen
    public function destroy(Materiaal $materiaal)
    {
        if (Auth::user()->role_id !== Role::STOCKBEHEERDER) {
            abort(403);
        }

        $materiaal->delete();

        return redirect()->route('materialen.beheer');
    }

    // Toon bewerkingpagina voor materialen
    public function edit(Materiaal $materiaal)
    {
        $subcategorieen = MateriaalSubcategorie::all();

        return view('pages.materialen-edit', compact('materiaal', 'subcategorieen'));
    }

    // Materialen bijwerken
    public function update(Request $request, Materiaal $materiaal)
    {
        $data = [
            'naam' => $request->naam,
            'beschrijving' => $request->beschrijving,
            'materiaal_subcategorie_id' => $request->materiaal_subcategorie_id,
            'belangrijk' => $request->has('belangrijk'),
        ];

        if ($request->has('verwijder_foto') && $materiaal->foto) {
            Storage::disk('public')->delete($materiaal->foto);
            $data['foto'] = null;
        }

        if ($request->hasFile('foto')) {
            if ($materiaal->foto) {
                Storage::disk('public')->delete($materiaal->foto);
            }

            $data['foto'] = $request->file('foto')->store('materialen', 'public');
        }

        $materiaal->update($data);

        return redirect()->route('materialen.beheer');
    }
}
