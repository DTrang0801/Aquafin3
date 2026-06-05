<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    use SoftDeletes;

    protected $table = 'materiaal_categorieen';

    protected $fillable = [
        'naam',
    ];

    public function subcategorieen()
    {
        return $this->hasMany(MateriaalSubcategorie::class, 'materiaal_categorie_id');
    }
}
