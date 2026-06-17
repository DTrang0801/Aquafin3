<?php

namespace App\Casts;

use App\Enums\FloodRiskLevel;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Custom cast for the belangrijk column that safely handles invalid enum values.
 * Converts invalid values (like old boolean 0/1) to null, preventing ValueError.
 */
class NullableFloodRiskLevelCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?FloodRiskLevel
    {
        if ($value === null) {
            return null;
        }

        try {
            return FloodRiskLevel::from((string) $value);
        } catch (\ValueError $e) {
            // If the value is not a valid enum case (e.g., old boolean 0/1),
            // return null and update the database to prevent future errors
            $model->update([$key => null]);

            return null;
        }
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof FloodRiskLevel) {
            return $value->value;
        }

        return (string) $value;
    }
}
