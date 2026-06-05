<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Belangrijk extends Model
{
    use HasFactory;

    protected $table = 'belangrijkeItems';

    protected $fillable = [
        'materiaal_id',
    ];

    public function materiaal(): BelongsTo
    {
        return $this->belongsTo(Materiaal::class, 'materiaal_id');
    }
}
