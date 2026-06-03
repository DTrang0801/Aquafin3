<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model: Mandje
 *
 * Beschrijving:
 * Voorstelling van een gebruikersmandje (cart). Een mandje hoort bij één
 * `User` en bevat meerdere `Materiaal`-items via de `mandje_materialen` pivot.
 *
 * Relaties:
 * - `gebruiker()` : belongsTo `User` via `gebruiker_id`.
 * - `materialen()` : belongsToMany `Materiaal` via `mandje_materialen` pivot
 *   (pivot model: `MandjeMateriaal`, pivot velden bevatten `aantal`).
 */
class Mandje extends Model
{
    protected $table = 'mandjes';

    protected $fillable = [
        'gebruiker_id',
    ];

    public function gebruiker()
    {
        return $this->belongsTo(User::class, 'gebruiker_id');
    }

    public function materialen()
    {
        return $this->belongsToMany(Materiaal::class, 'mandje_materialen', 'mandje_id', 'materiaal_id')
                    ->withPivot('id', 'aantal')
                    ->withTimestamps()
                    ->using(MandjeMateriaal::class);
    }
}
