<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materiaal extends Model
{
    protected $table = 'materialen';

    protected $fillable = [
        'materiaal_categorie_id',
        'materiaal_subcategorie_id',
        'naam',
        'beschrijving',
        'belangrijk',
    ];

    public function categorie()
    {
        return $this->belongsTo(Materiaalcategorie::class, 'materiaal_categorie_id');
    }

    public function subcategorie()
    {
        return $this->belongsTo(MateriaalSubcategorie::class, 'materiaal_subcategorie_id');
    }
}
