<?php

namespace App\Http\Controllers;

use App\Models\Mandje;
use Illuminate\Support\Facades\Auth;

class MandjeController extends Controller
{
    public function index()
    {
        $mandje = Mandje::where('gebruiker_id', Auth::id())->first();
        $materialen = $mandje ? $mandje->materialen : collect();
        return view('pages.winkelmandje', compact('materialen'));
    }
}