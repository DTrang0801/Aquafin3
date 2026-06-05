<?php

namespace App\Enums;

enum Province: string
{
    case VlaamsBrabant = 'Vlaams-Brabant';
    case WestVlaanderen = 'West-Vlaanderen';
    case OostVlaanderen = 'Oost-Vlaanderen';
    case Limburg = 'Limburg';
    case Antwerpen = 'Antwerpen';

    public function getDepotAddress(): string
    {
        return match ($this) {
            self::VlaamsBrabant => 'Depot Vlaams-Brabant',
            self::WestVlaanderen => 'Depot West-Vlaanderen',
            self::OostVlaanderen => 'Depot Oost-Vlaanderen',
            self::Limburg => 'Depot Limburg',
            self::Antwerpen => 'Depot Antwerpen',
        };
    }

    public static function options(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->value, self::cases())
        );
    }
}
