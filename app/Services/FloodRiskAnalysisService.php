<?php

namespace App\Services;

use App\Models\Neerslag;
use Illuminate\Support\Carbon;

/**
 * Service for analyzing historical rainfall patterns and predicting flood risk.
 * Provides 5-year forecasts and seasonal trend analysis.
 */
class FloodRiskAnalysisService
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

    /**
     * Analyze 5-year flood risk forecast based on historical rainfall patterns and trends.
     *
     * @return array<int, array<string, mixed>> Array of years with seasonal analysis
     */
    public function getFiveYearFloodRiskForecast(): array
    {
        $today = Carbon::now();
        $currentYear = $today->year;
        $nextFiveYears = range($currentYear + 1, $currentYear + 5);

        $seasonalTrends = $this->calculateSeasonalTrends();
        $result = [];

        foreach ($nextFiveYears as $forecastYear) {
            $yearsAhead = $forecastYear - $currentYear;
            $result[$forecastYear] = $this->analyzeYearWithTrend($forecastYear, $seasonalTrends, $yearsAhead);
        }

        return $result;
    }

    /**
     * Calculate average rainfall per season from historical data (2004-2025).
     *
     * @return array<string, array<string, float>> Seasonal averages with min/max/avg
     */
    private function calculateSeasonalAverages(): array
    {
        $neerslag = Neerslag::query()
            ->orderBy('jaar')
            ->orderBy('maand')
            ->get();

        $seasonalTotals = [];

        foreach ($neerslag as $record) {
            $season = $this->seasonForMonth($record->maand);
            $seasonKey = "{$record->jaar}_{$season}";

            if (! isset($seasonalTotals[$seasonKey])) {
                $seasonalTotals[$seasonKey] = [
                    'season' => $season,
                    'year' => $record->jaar,
                    'total' => 0,
                ];
            }

            $seasonalTotals[$seasonKey]['total'] += $record->mm;
        }

        $averages = [];
        foreach (array_keys(self::SEASON_MONTHS) as $season) {
            $seasonData = array_filter($seasonalTotals, fn ($item) => $item['season'] === $season);
            $totals = array_map(fn ($item) => $item['total'], $seasonData);

            if (empty($totals)) {
                $averages[$season] = [
                    'average' => 0,
                    'min' => 0,
                    'max' => 0,
                    'count' => 0,
                ];

                continue;
            }

            $averages[$season] = [
                'average' => (float) (array_sum($totals) / count($totals)),
                'min' => (float) min($totals),
                'max' => (float) max($totals),
                'count' => count($totals),
            ];
        }

        return $averages;
    }

    /**
     * Calculate seasonal trends over time to project future rainfall.
     *
     * @return array<string, array<string, mixed>> Trends including slope, standard deviation, and historical values
     */
    private function calculateSeasonalTrends(): array
    {
        $neerslag = Neerslag::query()
            ->orderBy('jaar')
            ->orderBy('maand')
            ->get();

        $seasonalTotals = [];

        foreach ($neerslag as $record) {
            $season = $this->seasonForMonth($record->maand);
            $seasonKey = "{$record->jaar}_{$season}";

            if (! isset($seasonalTotals[$seasonKey])) {
                $seasonalTotals[$seasonKey] = [
                    'season' => $season,
                    'year' => $record->jaar,
                    'total' => 0,
                ];
            }

            $seasonalTotals[$seasonKey]['total'] += $record->mm;
        }

        $trends = [];

        foreach (array_keys(self::SEASON_MONTHS) as $season) {
            $seasonData = array_filter($seasonalTotals, fn ($item) => $item['season'] === $season);

            // Sort by year to ensure chronological order for trend calculation
            usort($seasonData, fn ($a, $b) => $a['year'] <=> $b['year']);

            $values = array_map(fn ($item) => $item['total'], $seasonData);
            $years = array_map(fn ($item) => $item['year'], $seasonData);

            if (empty($values) || count($values) < 2) {
                $trends[$season] = [
                    'slope' => 0,
                    'average' => 0,
                    'stddev' => 0,
                    'min' => 0,
                    'max' => 0,
                    'values_by_year' => [],
                ];

                continue;
            }

            // Calculate linear regression slope
            $slope = $this->calculateTrendSlope($years, $values);

            // Calculate standard deviation for variance
            $mean = array_sum($values) / count($values);
            $squareDiffs = array_map(fn ($v) => ($v - $mean) ** 2, $values);
            $stddev = sqrt(array_sum($squareDiffs) / count($squareDiffs));

            $trends[$season] = [
                'slope' => $slope,
                'average' => $mean,
                'stddev' => $stddev,
                'min' => min($values),
                'max' => max($values),
                'values_by_year' => array_combine($years, $values) ?: [],
            ];
        }

        return $trends;
    }

    /**
     * Calculate linear regression slope for trend analysis.
     *
     * @param  array<int>  $years
     * @param  array<float>  $values
     */
    private function calculateTrendSlope(array $years, array $values): float
    {
        $n = count($years);
        if ($n < 2) {
            return 0;
        }

        $meanX = array_sum($years) / $n;
        $meanY = array_sum($values) / $n;

        $numerator = 0;
        $denominator = 0;

        foreach ($years as $i => $year) {
            $numerator += ($year - $meanX) * ($values[$i] - $meanY);
            $denominator += ($year - $meanX) ** 2;
        }

        if ($denominator === 0) {
            return 0;
        }

        return $numerator / $denominator;
    }

    /**
     * Analyze a specific year using trend-based forecasting.
     *
     * @param  int  $yearsAhead  Number of years in the future
     * @param  array<string, array<string, mixed>>  $trends
     * @return array<string, mixed>
     */
    private function analyzeYearWithTrend(int $year, array $trends, int $yearsAhead): array
    {
        $seasons = [];
        $baseYear = Carbon::now()->year;

        foreach (array_keys(self::SEASON_MONTHS) as $season) {
            $trend = $trends[$season];
            $threshold = self::SEASON_THRESHOLDS_MM[$season];

            // Project rainfall based on trend
            $projectedRainfall = $this->projectRainfallForYear(
                $trend['average'],
                $trend['slope'],
                $yearsAhead,
                $trend['stddev']
            );

            $riskLevel = $this->calculateRiskLevel($projectedRainfall, $threshold);

            $seasons[$season] = [
                'forecast_mm' => (int) round($projectedRainfall),
                'threshold_mm' => $threshold,
                'risk_level' => $riskLevel,
                'exceeds_threshold' => $projectedRainfall >= $threshold,
                'variance_percent' => (($projectedRainfall - $threshold) / $threshold) * 100,
                'trend_based' => true,
            ];
        }

        $riskCount = collect($seasons)
            ->filter(fn ($s) => $s['exceeds_threshold'])
            ->count();

        return [
            'year' => $year,
            'seasons' => $seasons,
            'at_risk_seasons' => $riskCount,
            'overall_risk' => $riskCount >= 2 ? 'high' : ($riskCount === 1 ? 'medium' : 'low'),
        ];
    }

    /**
     * Project rainfall for a future year based on trend and variability.
     *
     * @param  float  $trend  The slope (mm per year)
     * @param  int  $yearsAhead  Number of years in the future
     * @param  float  $stddev  Standard deviation for variance
     */
    private function projectRainfallForYear(
        float $historicalAverage,
        float $trend,
        int $yearsAhead,
        float $stddev
    ): float {
        // Base projection: historical average + trend over time
        $projectedValue = $historicalAverage + ($trend * $yearsAhead);

        // Add cyclical variance based on historical standard deviation
        // Creates natural variability year-to-year using a pseudo-random but deterministic pattern
        $varianceFactor = sin($yearsAhead * 0.5) * ($stddev * 0.3);

        return max($projectedValue + $varianceFactor, 0);
    }

    /**
     * Determine risk level based on rainfall vs threshold.
     *
     * @return 'low'|'medium'|'high'
     */
    private function calculateRiskLevel(float $rainfall, int $threshold): string
    {
        if ($rainfall >= $threshold * 1.2) {
            return 'high';
        }

        if ($rainfall >= $threshold) {
            return 'medium';
        }

        return 'low';
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

    /**
     * Get seasonal analysis for the current year to date.
     *
     * @return array<string, mixed>
     */
    public function getCurrentYearAnalysis(): array
    {
        $today = Carbon::now();
        $currentYear = $today->year;
        $currentMonth = $today->month;
        $currentSeason = $this->seasonForMonth($currentMonth);

        if ($currentSeason === null) {
            return [];
        }

        $seasonMonths = self::SEASON_MONTHS[$currentSeason];
        $currentSeasonData = Neerslag::query()
            ->where('jaar', $currentYear)
            ->whereIn('maand', $seasonMonths)
            ->get();

        $totalRainfall = (int) $currentSeasonData->sum('mm');
        $threshold = self::SEASON_THRESHOLDS_MM[$currentSeason];
        $monthsCompleted = $currentSeasonData->count();
        $monthsInSeason = count($seasonMonths);

        return [
            'season' => $currentSeason,
            'total_rainfall' => $totalRainfall,
            'threshold' => $threshold,
            'months_completed' => $monthsCompleted,
            'months_in_season' => $monthsInSeason,
            'risk_level' => $this->calculateRiskLevel($totalRainfall, $threshold),
            'exceeds_threshold' => $totalRainfall >= $threshold,
            'percentage' => (($totalRainfall / $threshold) * 100),
        ];
    }
}
