<?php

namespace App\Http\Controllers;

use App\Models\Materiaal;
use Illuminate\View\View;

class StockDashboardController extends Controller
{
    public function index(): View
    {
        $mostOrderedItems = Materiaal::query()
            ->with('subcategorie.categorie')
            ->where('order_count', '>', 0)
            ->orderByDesc('order_count')
            ->limit(20)
            ->get();

        return view('pages.stock-dashboard', [
            'mostOrderedItems' => $mostOrderedItems,
        ]);
    }
}
