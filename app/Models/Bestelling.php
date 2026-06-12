<?php

namespace App\Models;

use Database\Factories\BestellingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: Bestelling
 *
 * Beschrijving:
 * Een bestelling geplaatst door een gebruiker (technieker).
 * Een bestelling bevat data zoals gevraagde datum, locatie en een
 * algemene opmerking.
 *
 * Relaties:
 * - `gebruiker()` : belongsTo `User` via `gebruiker_id`.
 * - `materialen()` : belongsToMany `Materiaal` via `bestelling_materialen` pivot
 *   (slaat per item het `aantal` op).
 */
class Bestelling extends Model
{
    /** @use HasFactory<BestellingFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'bestellingen';

    protected $fillable = [
        'gebruiker_id',
        'gevraagde_datum',
        'gevraagde_tijd',
        'locatie',
        'opmerking',
        'custom_location_used',
        'is_edited',
        'can_edit_until',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $bestelling) {
            if ($bestelling->can_edit_until === null) {
                $bestelling->can_edit_until = now()->addDay();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'custom_location_used' => 'bool',
            'is_edited' => 'bool',
            'can_edit_until' => 'datetime',
        ];
    }

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

    public function canStillBeEdited(): bool
    {
        if ($this->can_edit_until === null) {
            return false;
        }

        return now()->isBefore($this->can_edit_until);
    }

    public function markAsEdited(): void
    {
        $this->update(['is_edited' => true]);
    }
}
