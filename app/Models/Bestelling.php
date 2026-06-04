<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model: Bestelling
 *
 * Beschrijving:
 * Een bestelling geplaatst door een gebruiker (technieker).
 * Een bestelling bevat data zoals gevraagde datum/tijd, locatie en een
 * algemene opmerking.
 *
 * Relaties:
 * - `gebruiker()` : belongsTo `User` via `gebruiker_id`.
 * - `materialen()` : belongsToMany `Materiaal` via `bestelling_materialen` pivot
 *   (slaat per item het `aantal` op).
 */
class Bestelling extends Model
{
    protected $table = 'bestellingen';

    protected $fillable = [
        'gebruiker_id',
        'gevraagde_datum',
        'gevraagde_tijd',
        'locatie',
        'opmerking',
    ];

    public function gebruiker()
    {
        return $this->belongsTo(User::class, 'gebruiker_id');
    }

    public function materialen()
    {
        return $this->belongsToMany(Materiaal::class, 'bestelling_materialen', 'bestelling_id', 'materiaal_id')
                    ->withPivot('aantal')
                    ->withTimestamps();
    }
}
