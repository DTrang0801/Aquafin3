<?php

namespace App\View\Components;

use App\Models\MateriaalSubcategorie;
use Illuminate\View\Component;
use Illuminate\View\View;

class AddMaterialForm extends Component
{
    public function render(): View
    {
        return view('components.add-material-form', [
            'subcategorieen' => MateriaalSubcategorie::with('categorie')
                ->orderBy('naam')
                ->get()
                ->groupBy('categorie.naam'),
        ]);
    }
}
