<?php

namespace App\Enums;

enum FloodRiskLevel: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    /**
     * Returns true when this level is at least as severe as the required level.
     * Used to determine whether a material should be flagged given its minimum trigger level.
     */
    public function meetsOrExceeds(self $required): bool
    {
        return $this->numericValue() >= $required->numericValue();
    }

    private function numericValue(): int
    {
        return match ($this) {
            self::Low => 1,
            self::Medium => 2,
            self::High => 3,
        };
    }

    /** Dutch display label for this risk level. */
    public function label(): string
    {
        return match ($this) {
            self::Low => 'Laag',
            self::Medium => 'Gemiddeld',
            self::High => 'Hoog',
        };
    }
}
