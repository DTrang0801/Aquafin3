<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model: Materiaal
 *
 * Beschrijving:
 * Vertegenwoordigt een materiaal of artikel dat besteld of in een mandje gezet
 * kan worden. Behoort tot één `MateriaalSubcategorie` (via
 * `materiaal_subcategorie_id`). De hoofdcategorie wordt afgeleid via de
 * subcategorie.
 *
 * Relaties:
 * - `subcategorie()` : belongsTo `MateriaalSubcategorie` via
 *   `materiaal_subcategorie_id`.
 * - `categorie()` : afgeleid via `subcategorie()->categorie()`.
 * - `bestellingen()` : belongsToMany `Bestelling` via `bestelling_materialen`
 *   pivot (bevat `aantal`).
 */
class Materiaal extends Model
{
    protected $table = 'materialen';

    protected $fillable = [
        'materiaal_subcategorie_id',
        'naam',
        'beschrijving',
        'foto',
        'belangrijk',
    ];

    public function categorie()
    {
        return $this->subcategorie ? $this->subcategorie->categorie : null;
    }

    public function subcategorie()
    {
        return $this->belongsTo(MateriaalSubcategorie::class, 'materiaal_subcategorie_id');
    }

    public function bestellingen()
    {
        return $this->belongsToMany(Bestelling::class, 'bestelling_materialen', 'materiaal_id', 'bestelling_id')
            ->withPivot('aantal')
            ->withTimestamps();
    }
}
