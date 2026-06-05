<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Province;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model: User
 *
 * Beschrijving:
 * Vertegenwoordigt een gebruiker van het systeem. Standaard Laravel
 * `Authenticatable`-model met extra relaties naar bestellingen en mandjes.
 *
 * Relaties:
 * - `bestellingen()` : hasMany `Bestelling` via `gebruiker_id`.
 * - `mandjes()` : hasMany `Mandje` via `gebruiker_id`.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'province',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function bestellingen()
    {
        return $this->hasMany(Bestelling::class, 'gebruiker_id');
    }

    public function mandjes()
    {
        return $this->hasMany(Mandje::class, 'gebruiker_id');
    }

    public function getDepotLocation(): ?string
    {
        if (! $this->province) {
            return null;
        }

        try {
            $province = Province::from($this->province);

            return $province->getDepotAddress();
        } catch (\ValueError) {
            return null;
        }
    }
}
