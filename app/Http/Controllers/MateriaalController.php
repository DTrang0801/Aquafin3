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

    public function index()
    {
        // Categorieen ophalen met hun subcategorieën en materialen die in de subcategorieën zitten.
        $categorieen = Materiaalcategorie::with('subcategorieen.materialen')->get();

        return view('materiaallijst', compact('categorieen'));
    }
}