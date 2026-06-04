<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class FloodRiskService
{

    public function archiveHistoricalMonth($year, $month, $lat = 50.75, $lon = 4.5): void
    {
        // Check if this month is already archived to prevent duplicate rows
        $exists = DB::table('neerslags')
            ->where('jaar', $year)
            ->where('maand', $month)
            ->exists();

        if ($exists) {
            return;
        }

        // Determine the start and end dates of the target month
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');

        // Open-Meteo requires the 'archive' endpoint for historical dates older than a few weeks
        $response = Http::get("https://archive-api.open-meteo.com/v1/archive", [
            'latitude' => $lat,
            'longitude' => $lon,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'daily' => 'rain_sum',
            'timezone' => 'Europe/Berlin'
        ]);

        if ($response->successful()) {
            $dailyRain = $response->json()['daily']['rain_sum'] ?? [];
            
            // Sum up every day of that month
            $monthlyTotal = array_sum($dailyRain);

            // Insert into your database table matching your Seeder structure
            DB::table('neerslags')->insert([
                'jaar' => $year,
                'maand' => $month,
                'mm' => (int) round($monthlyTotal),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Log::info("Neerslag succesvol gearchiveerd voor {$year}-{$month}: {$monthlyTotal} mm");
        } else {
            // Log::error("Fout bij het ophalen van historische neerslag voor {$year}-{$month}");
        }
    }

    private array $thresholds = [
        'Winter' => 300,
        'Lente'  => 250,
        'Zomer'  => 260,
        'Herfst' => 280
    ];

    private array $seasonMonths = [
        'Winter' => [12, 1, 2],
        'Lente'  => [3, 4, 5],
        'Zomer'  => [6, 7, 8],
        'Herfst' => [9, 10, 11]
    ];

    public function checkAndFlagItems($lat = 50.75, $lon = 4.5): bool
    {
        $now = Carbon::now('Europe/Berlin');
        $currentMonth = $now->month;
        $currentYear = $now->year;

        //Huidige seizoen bepalen op basis van maand
        $currentSeason = null;
        foreach ($this->seasonMonths as $season => $months) {
            if (in_array($currentMonth, $months)) {
                $currentSeason = $season;
                break;
            }
        }

        $targetMonths = $this->seasonMonths[$currentSeason];
        $totalRainfallAccumulated = 0;

        foreach ($targetMonths as $month) {
            if ($month !== $currentMonth) {

                $queryYear = ($currentSeason === 'Winter' && $month === 12) ? $currentYear - 1 : $currentYear;

                $dbRecord = DB::table('neerslags')
                    ->where('jaar', $queryYear)
                    ->where('maand', $month)
                    ->first();

                if ($dbRecord) {
                    $totalRainfallAccumulated += $dbRecord->mm;
                }
            }
        }

        $response = Http::withOptions([
        'curl' => [
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2, // Force TLS 1.2 negotiation explicitly
            // If it still gives an error, you can also add a timeout constraint:
            CURLOPT_CONNECTTIMEOUT => 10,
        ]
        ])->get("https://api.open-meteo.com/v1/forecast", [
            'latitude' => $lat,
            'longitude' => $lon,
            'daily' => 'rain_sum',
            'timezone' => 'Europe/Berlin',
            'past_days' => 30,
            'forecast_days' => 14
        ]);

        if ($response->successful()) {
            $daily = $response->json()['daily'] ?? [];
            if (isset($daily['time'])) {
                foreach ($daily['time'] as $index => $dateString) {
                    $date = Carbon::parse($dateString);
                    
                    if ($date->month === $currentMonth) {
                        $totalRainfallAccumulated += ($daily['rain_sum'][$index] ?? 0);
                    }
                }
            }
        }

        // Compare
        $threshold = $this->thresholds[$currentSeason];
        $isFloodRiskActive = $totalRainfallAccumulated >= $threshold;
        $floodMaterialIds = DB::table('belangrijkeItems')->pluck('materiaal_id')->toArray();

        if ($isFloodRiskActive) {
            // Zet all important items to true
           if (!empty($floodMaterialIds)) {
                DB::table('materialen')
                    ->whereIn('id', $floodMaterialIds)
                    ->update(['belangrijk' => true]);
            }

            // Set everything else to false
            DB::table('materialen')
                ->whereNotIn('id', $floodMaterialIds)
                ->update(['belangrijk' => false]);
                
        } else {
            // Zet alles op niet belangrijk
            if (!empty($floodMaterialIds)) {
                DB::table('materialen')
                    ->whereIn('id', $floodMaterialIds)
                    ->update(['belangrijk' => false]);
            }

            // Set everything else to false
            DB::table('materialen')
                ->whereNotIn('id', $floodMaterialIds)
                ->update(['belangrijk' => false]);
        }

        return $isFloodRiskActive;
    }
}