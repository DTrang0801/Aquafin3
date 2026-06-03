<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Neerslag extends Model
{
    protected $fillable = [
        'jaar',
        'maand',
        'mm',
    ];
}
