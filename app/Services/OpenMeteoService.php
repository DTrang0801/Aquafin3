<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service voor de Open-Meteo weerinformatie API.
 * Standaardlocatie is ingesteld op Brussel, België (50.8503°N, 4.3517°E).
 */
class OpenMeteoService
{
    /** Latitude voor Brussel*/
    public const DEFAULT_LATITUDE = 50.8503;

    /** Longitude voor Brussel*/
    public const DEFAULT_LONGITUDE = 4.3517;

    /**
     * Los coördinaten op met standaardwaarden als er geen zijn opgegeven.
     * Dit zorgt ervoor dat API-aanroepen altijd geldige coördinaten hebben.
     *
     * @param  float|null  $latitude  Opgegeven breedte (optioneel)
     * @param  float|null  $longitude  Opgegeven lengte (optioneel)
     * @return array{lat: float, lon: float} Opgeloste coördinaten
     */
    public function resolveCoordinates(?float $latitude, ?float $longitude): array
    {
        return [
            'lat' => $latitude ?? self::DEFAULT_LATITUDE,
            'lon' => $longitude ?? self::DEFAULT_LONGITUDE,
        ];
    }

    /**
     * Haal weersvoorspelling op van de Open-Meteo API.
     * Retourneert zowel huidige neerslag als een 14-daagse voorspelling met historische gegevens (afgelopen 30 dagen).
     *
     * @return array<string, mixed>|null Neerslaggegevens of null als API-aanroep mislukt
     */
    public function fetchForecast(float $latitude, float $longitude): ?array
    {
        try {
            $response = Http::timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'daily' => 'rain_sum',
                'current' => 'rain,precipitation',
                'timezone' => 'auto',
                'past_days' => 30,
                'forecast_days' => 14,
            ]);

            if ($response->failed()) {
                Log::warning('Open-Meteo API request failed', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'status_code' => $response->status(),
                    'response' => $response->body(),
                ]);

                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Open-Meteo API exception', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return null;
        }
    }

    /**
     * Haal gearchiveerde historische neerslaggegevens voor een bepaalde maand op.
     * Gebruikt voor het vullen van de Neerslag (neerslag) tabel met historische gegevens.
     *
     * @param  float  $latitude  Locatiebreedte
     * @param  float  $longitude  Locatielengte
     * @param  int  $year  Jaar voor het ophalen van gegevens
     * @param  int  $month  Maand voor het ophalen van gegevens
     * @return float|null Totale neerslag in millimeters voor de maand, of null als API-aanroep mislukt
     */
    public function fetchArchivedMonthlyRain(
        float $latitude,
        float $longitude,
        int $year,
        int $month,
    ): ?float {
        try {
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');

            $response = Http::timeout(10)->get('https://archive-api.open-meteo.com/v1/archive', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'daily' => 'rain_sum',
                'timezone' => 'Europe/Berlin',
            ]);

            if ($response->failed()) {
                Log::warning('Open-Meteo Archive API request failed', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'year' => $year,
                    'month' => $month,
                    'status_code' => $response->status(),
                ]);

                return null;
            }

            $dailyRain = $response->json()['daily']['rain_sum'] ?? [];

            return (float) array_sum($dailyRain);
        } catch (\Exception $e) {
            Log::error('Open-Meteo Archive API exception', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'year' => $year,
                'month' => $month,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Parse voorspellings-API-antwoord voor weergave in de UI.
     * Scheidt historische neerslag (afgelopen maand) van toekomstige voorspellingen.
     * Formatteert dagnamen.
     *
     * @param  array<string, mixed>  $data  Ruwe voorspellingsgegevens van Open-Meteo API
     * @return array{
     *     currentRain: float,
     *     pastMonthTotal: float,
     *     dailyRainForecast: list<array{day_name: string, amount: float}>,
     *     timezone: string,
     *     daily: array<string, mixed>|null
     * }
     */
    public function parseForecastForDisplay(array $data): array
    {
        $current = $data['current'] ?? null;
        $daily = $data['daily'] ?? null;
        $timezone = $data['timezone'] ?? 'Europe/Berlin';

        $pastMonthTotal = 0.0;
        $dailyRainForecast = [];

        if ($daily && isset($daily['time'])) {
            $today = Carbon::today($timezone);

            foreach ($daily['time'] as $index => $dateString) {
                $date = Carbon::parse($dateString, $timezone);
                $amount = (float) ($daily['rain_sum'][$index] ?? 0);

                // Verzamel historische neerslaggegevens voor de afgelopen maand
                if ($date->isBefore($today)) {
                    $pastMonthTotal += $amount;
                } else {
                    // Bouw toekomstige voorspellingsinvoeren op
                    $dailyRainForecast[] = [
                        'day_name' => $date->locale('nl')->isoFormat('dddd D MMM'),
                        'amount' => $amount,
                    ];
                }
            }
        }

        return [
            'currentRain' => (float) ($current['precipitation'] ?? 0),
            'pastMonthTotal' => round($pastMonthTotal, 1),
            'dailyRainForecast' => $dailyRainForecast,
            'timezone' => $timezone,
            'daily' => $daily,
        ];
    }

    /**
     * Tel neerslag voor een specifieke maand op vanuit de dagelijkse voorspellingsgegevens.
     * Gebruikt voor het berekenen van verwachte neerslag voor de huidige/toekomstige maanden.
     *
     * @param  array<string, mixed>|null  $daily  Dagelijkse voorspellingsgegevens van API
     * @param  int  $month  Maandnummer (1-12) om neerslag voor op te tellen
     * @param  string  $timezone  Tijdzone voor datumanalyse
     * @return float Totale neerslag in millimeters voor de maand
     */
    public function sumRainfallForMonth(?array $daily, int $month, string $timezone = 'Europe/Berlin'): float
    {
        if (! $daily || ! isset($daily['time'])) {
            return 0.0;
        }

        $total = 0.0;

        foreach ($daily['time'] as $index => $dateString) {
            $date = Carbon::parse($dateString, $timezone);

            if ($date->month === $month) {
                $total += (float) ($daily['rain_sum'][$index] ?? 0);
            }
        }

        return $total;
    }
}
