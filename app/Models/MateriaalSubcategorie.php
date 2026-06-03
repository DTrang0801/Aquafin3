<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model: MateriaalSubcategorie
 *
 * Beschrijving:
 * Dit model vertegenwoordigt een subcategorie van materialen. Een subcategorie
 * hoort bij één `Materiaalcategorie` en bevat meerdere `Materiaal` records.
 *
 * Relaties:
 * - `categorie()` : belongsTo `Materiaalcategorie` via `materiaal_categorie_id`.
 * - `materialen()` : hasMany `Materiaal` via `materiaal_subcategorie_id`.
 */
class MateriaalSubcategorie extends Model
{
    protected $table = 'materiaal_subcategorieen';

    protected $fillable = [
        'materiaal_categorie_id',
        'naam',
    ];

    public function categorie()
    {
        return $this->belongsTo(Materiaalcategorie::class, 'materiaal_categorie_id');
    }

    public function materialen()
    {
        return $this->hasMany(Materiaal::class, 'materiaal_subcategorie_id');
    }
}
