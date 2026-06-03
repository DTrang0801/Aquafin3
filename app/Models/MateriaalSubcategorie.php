<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
