<?php

namespace App\Http\Controllers;

use App\Models\Materiaal;

class FavorietenController extends Controller
{
    public function index()
    {
        $favorieten = Materiaal::withCount('bestellingen')
            ->orderBy('bestellingen_count', 'desc')
            ->take(9)
            ->get();

        return view('pages.favorieten', compact('favorieten'));
    }
}