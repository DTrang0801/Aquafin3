<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class OpenMeteoService
{
    public const DEFAULT_LATITUDE = 50.8503;

    public const DEFAULT_LONGITUDE = 4.3517;

    /**
     * @return array{lat: float, lon: float}
     */
    public function resolveCoordinates(?float $latitude, ?float $longitude): array
    {
        return [
            'lat' => $latitude ?? self::DEFAULT_LATITUDE,
            'lon' => $longitude ?? self::DEFAULT_LONGITUDE,
        ];
    }

    public function fetchForecast(float $latitude, float $longitude): ?array
    {
        $response = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'daily' => 'rain_sum',
            'current' => 'rain,precipitation',
            'timezone' => 'auto',
            'past_days' => 30,
            'forecast_days' => 14,
        ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    public function fetchArchivedMonthlyRain(
        float $latitude,
        float $longitude,
        int $year,
        int $month,
    ): ?float {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');

        $response = Http::get('https://archive-api.open-meteo.com/v1/archive', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'daily' => 'rain_sum',
            'timezone' => 'Europe/Berlin',
        ]);

        if ($response->failed()) {
            return null;
        }

        $dailyRain = $response->json()['daily']['rain_sum'] ?? [];

        return (float) array_sum($dailyRain);
    }

    /**
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

                if ($date->isBefore($today)) {
                    $pastMonthTotal += $amount;
                } else {
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
     * @param  array<string, mixed>|null  $daily
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
