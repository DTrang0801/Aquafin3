<?php

namespace App\Http\Controllers;

use App\Models\Materiaal;
use Illuminate\View\View;

class StockDashboardController extends Controller
{ // Toont de stock dashboard pagina met de 20 meest bestelde materialen

    public function index(): View
    {
        $mostOrderedItems = Materiaal::query()
            ->with('subcategorie.categorie') 
            ->where('order_count', '>', 0) // Materialen die minstens 1 keer besteld zijn
            ->orderByDesc('order_count') // Sorteer van meest naar minst besteld
            ->limit(20)
            ->get();

        return view('pages.stock-dashboard', [
            'mostOrderedItems' => $mostOrderedItems,
        ]);
    }
}
