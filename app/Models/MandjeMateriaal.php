<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Pivot Model: MandjeMateriaal
 *
 * Beschrijving:
 * Pivot-tabel tussen `Mandje` en `Materiaal`. Bewaart de hoeveelheid
 * (`aantal`) van een materiaal in een mandje en gebruikt timestamps.
 *
 * Velden:
 * - `mandje_id`, `materiaal_id`, `aantal`, timestamps
 *
 * Gebruik:
 * - Wordt gebruikt als pivotclass in `Mandje::materialen()`.
 */
class MandjeMateriaal extends Pivot
{
    protected $table = 'mandje_materialen';

    protected $fillable = [
        'mandje_id',
        'materiaal_id',
        'aantal',
    ];

    public $timestamps = true;
}
