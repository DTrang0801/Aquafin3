<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model: Neerslag
 *
 * Beschrijving:
 * Slaat historische neerslaggegevens op per jaar/maand in millimeter (mm).
 * Wordt gebruikt voor rapportage en historische analyses.
 *
 * Velden:
 * - `jaar`, `maand`, `mm`
 */
class Neerslag extends Model
{
    protected $fillable = [
        'jaar',
        'maand',
        'mm',
    ];
}
