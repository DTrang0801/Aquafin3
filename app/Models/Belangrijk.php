<?php

namespace App\Models;

use App\Enums\FloodRiskLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Belangrijk extends Model
{
    use HasFactory;

    protected $table = 'belangrijkeItems';

    protected $fillable = [
        'materiaal_id',
        'risk_level',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'risk_level' => FloodRiskLevel::class,
    ];

    public function materiaal(): BelongsTo
    {
        return $this->belongsTo(Materiaal::class, 'materiaal_id');
    }
}
