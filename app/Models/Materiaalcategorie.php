<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model: Materiaalcategorie
 *
 * Beschrijving:
 * Hoofd-categorie voor materialen. Een categorie kan meerdere
 * `MateriaalSubcategorie`-records (subcategorieën) hebben.
 *
 * Relaties:
 * - Subcategorieën zijn bereikbaar via `MateriaalSubcategorie::categorie()`.
 */
class Materiaalcategorie extends Model
{
    protected $table = 'materiaal_categorieen';

    protected $fillable = [
        'naam',
    ];
}
