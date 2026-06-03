<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materiaalcategorie extends Model
{
    protected $table = 'materiaal_categorieen';

    protected $fillable = [
        'naam',
    ];
}
