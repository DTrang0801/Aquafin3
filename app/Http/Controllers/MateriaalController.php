<?php

namespace App\Http\Controllers;

use App\Models\Materiaal;
use App\Models\Materiaalcategorie;
use App\Models\MateriaalSubcategorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MateriaalController extends Controller
{
    // Materiaal in een tabel tonen

    public function index(Request $request)
    {
        // Always display global important highlights at the top (excluding soft-deleted)
        $belangrijkeMaterialen = Materiaal::where('belangrijk', true)->with('subcategorie')->get();

        // 1. Fetch search inputs & parameters
        $search = $request->input('search');
        $selectedCatId = $request->input('category_id');
        $selectedSubcatId = $request->input('subcategory_id');

        // Fetch master sets for dropdown selectors
        $filterCategories = Materiaalcategorie::orderBy('naam', 'asc')->get();
        $filterSubcategories = collect();
        if ($selectedCatId) {
            $filterSubcategories = MateriaalSubcategorie::where('materiaal_categorie_id', $selectedCatId)
                ->orderBy('naam', 'asc')
                ->get();
        }

        // 2. Build the basic relational query (excludes soft-deleted by default)
        $query = Materiaalcategorie::with(['subcategorieen' => function ($q) use ($selectedSubcatId) {
            if ($selectedSubcatId) {
                $q->where('id', $selectedSubcatId);
            }
        }, 'subcategorieen.materialen']);

        if ($selectedCatId) {
            $query->where('id', $selectedCatId);
        }

        $rawCategories = $query->get();

        // Collections to track which accordions should be expanded ('open')
        $openCategoryIds = collect();
        $openSubcategoryIds = collect();

        // 3. Filter down the data structure and determine visibility
        $categorieen = $rawCategories->filter(function ($cat) use ($search, $openCategoryIds, $openSubcategoryIds) {
            $catMatch = $search ? $this->isTypoTolerantMatch($cat->naam, $search) : true;

            // Filter the subcategories inside this category
            $cat->setRelation('subcategorieen', $cat->subcategorieen->filter(function ($sub) use ($search, $catMatch, $openSubcategoryIds) {
                $subMatch = $search ? $this->isTypoTolerantMatch($sub->naam, $search) : true;

                // Filter down materials array inside this subcategory if neither category nor subcategory matched the query text
                if (! $subMatch && ! $catMatch && $search) {
                    $sub->setRelation('materialen', $sub->materialen->filter(function ($m) use ($search) {
                        return $this->isTypoTolerantMatch($m->naam, $search) ||
                               $this->isTypoTolerantMatch($m->beschrijving, $search);
                    }));
                }

                // Hide this subcategory completely if it contains no matching items
                if ($sub->materialen->isEmpty()) {
                    return false;
                }

                // If we are searching and there are items, force this subcategory to expand
                if ($search) {
                    $openSubcategoryIds->push($sub->id);
                }

                return true;
            }));

            // Hide this entire main category if it has no visible subcategories left
            if ($cat->subcategorieen->isEmpty()) {
                return false;
            }

            // If we are actively searching and it passed the checks, force this category to expand
            if ($search || request('category_id') || request('subcategory_id')) {
                $openCategoryIds->push($cat->id);
            }

            return true;
        });

        // 4. Fallback default context: if NO search is active, keep everything visible and open
        if (! $search && ! $selectedCatId && ! $selectedSubcatId) {
            $openCategoryIds = $categorieen->pluck('id');
            foreach ($categorieen as $cat) {
                $openSubcategoryIds = $openSubcategoryIds->merge($cat->subcategorieen->pluck('id'));
            }
        }

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
     * Typo-tolerant matching mechanism using Levenshtein distance computations.
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

    public function suggesties(Request $request)
    {
        $query = $request->input('q');

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $materialen = Materiaal::where('naam', 'like', '%'.$query.'%')
            ->orWhere('beschrijving', 'like', '%'.$query.'%')
            ->limit(10)
            ->get(['id', 'naam', 'materiaal_subcategorie_id'])
            ->load('subcategorie');

        return response()->json($materialen);
    }

    public function create()
    {
        $categorieen = Materiaalcategorie::orderBy('naam')->get();
        $subcategorieen = MateriaalSubcategorie::all();

        return view('pages.materialen-create', compact('categorieen', 'subcategorieen'));
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
        $materialen = Materiaal::with('subcategorie.categorie')->get();

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
