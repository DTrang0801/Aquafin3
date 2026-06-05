<?php

namespace App\Services;

use App\Models\Belangrijk;
use App\Models\Materiaal;
use App\Models\Neerslag;
use Illuminate\Support\Carbon;

class FloodRiskService
{
    private const SEASON_THRESHOLDS_MM = [
        'Winter' => 300,
        'Lente' => 250,
        'Zomer' => 260,
        'Herfst' => 280,
    ];

    /** @var array<string, list<int>> */
    private const SEASON_MONTHS = [
        'Winter' => [12, 1, 2],
        'Lente' => [3, 4, 5],
        'Zomer' => [6, 7, 8],
        'Herfst' => [9, 10, 11],
    ];

    public function __construct(private OpenMeteoService $openMeteo) {}

    public function archiveHistoricalMonth(
        int $year,
        int $month,
        float $latitude = OpenMeteoService::DEFAULT_LATITUDE,
        float $longitude = OpenMeteoService::DEFAULT_LONGITUDE,
    ): void {
        $exists = Neerslag::query()
            ->where('jaar', $year)
            ->where('maand', $month)
            ->exists();

        if ($exists) {
            return;
        }

        $monthlyTotal = $this->openMeteo->fetchArchivedMonthlyRain($latitude, $longitude, $year, $month);

        if ($monthlyTotal === null) {
            return;
        }

        Neerslag::query()->create([
            'jaar' => $year,
            'maand' => $month,
            'mm' => (int) round($monthlyTotal),
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $forecastDaily  Re-use daily payload from an existing forecast call.
     */
    public function checkAndFlagItems(
        float $latitude = OpenMeteoService::DEFAULT_LATITUDE,
        float $longitude = OpenMeteoService::DEFAULT_LONGITUDE,
        ?array $forecastDaily = null,
        string $timezone = 'Europe/Berlin',
    ): bool {
        $isFloodRiskActive = $this->evaluateFloodRisk($latitude, $longitude, $forecastDaily, $timezone);
        $this->applyMaterialFlags($isFloodRiskActive);

        return $isFloodRiskActive;
    }

    public function applySimulation(): bool
    {
        $this->applyMaterialFlags(true);

        return true;
    }

    /**
     * @param  list<int>  $materiaalIds
     */
    public function syncLinkedMaterials(array $materiaalIds): void
    {
        Belangrijk::query()->delete();

        if ($materiaalIds === []) {
            return;
        }

        $now = now();

        Belangrijk::query()->insert(
            collect($materiaalIds)
                ->map(fn (int $id): array => [
                    'materiaal_id' => $id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
                ->all()
        );
    }

    /**
     * @return list<int>
     */
    public function linkedMaterialIds(): array
    {
        return Belangrijk::query()
            ->pluck('materiaal_id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * @param  array<string, mixed>|null  $forecastDaily
     */
    private function evaluateFloodRisk(
        float $latitude,
        float $longitude,
        ?array $forecastDaily = null,
        string $timezone = 'Europe/Berlin',
    ): bool {
        $now = Carbon::now('Europe/Berlin');
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $currentSeason = $this->seasonForMonth($currentMonth);

        if ($currentSeason === null) {
            return false;
        }

        $totalRainfall = $this->accumulatedSeasonRainfall(
            $currentSeason,
            $currentMonth,
            $currentYear,
        );

        if ($forecastDaily === null) {
            $forecast = $this->openMeteo->fetchForecast($latitude, $longitude);
            $forecastDaily = $forecast['daily'] ?? null;
            $timezone = $forecast['timezone'] ?? $timezone;
        }

        $totalRainfall += $this->openMeteo->sumRainfallForMonth($forecastDaily, $currentMonth, $timezone);

        return $totalRainfall >= self::SEASON_THRESHOLDS_MM[$currentSeason];
    }

    private function accumulatedSeasonRainfall(string $season, int $currentMonth, int $currentYear): float
    {
        $total = 0.0;

        foreach (self::SEASON_MONTHS[$season] as $month) {
            if ($month === $currentMonth) {
                continue;
            }

            $queryYear = ($season === 'Winter' && $month === 12)
                ? $currentYear - 1
                : $currentYear;

            $record = Neerslag::query()
                ->where('jaar', $queryYear)
                ->where('maand', $month)
                ->first();

            if ($record) {
                $total += $record->mm;
            }
        }

        return $total;
    }

    private function seasonForMonth(int $month): ?string
    {
        foreach (self::SEASON_MONTHS as $season => $months) {
            if (in_array($month, $months, true)) {
                return $season;
            }
        }

        return null;
    }

    private function applyMaterialFlags(bool $isFloodRiskActive): void
    {
        $linkedIds = $this->linkedMaterialIds();

        if ($linkedIds === []) {
            Materiaal::query()->update(['belangrijk' => false]);

            return;
        }

        Materiaal::query()
            ->whereIn('id', $linkedIds)
            ->update(['belangrijk' => $isFloodRiskActive]);

        Materiaal::query()
            ->whereNotIn('id', $linkedIds)
            ->update(['belangrijk' => false]);
    }
}
