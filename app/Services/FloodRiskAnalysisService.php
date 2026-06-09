<?php

namespace App\Services;

use App\Models\Neerslag;
use Illuminate\Support\Carbon;

/**
 * Service voor analyse van historische neerslagpatronen en voorspelling van lange termijn overstroomingsrisico.
 * Biedt 5-jaar trendgebaseerde voorspellingen en analyseert de voortgang van het huidige seizoen naar overstroomingsdrempels.
 *
 * Gebruikt lineaire regressie op historische neerslaggegevens (2004-2025) om toekomstige neerslagpatronen te projecteren,
 * en vergelijkt vervolgens projecties met seizoensdrempels om overstroomingsrisiconiveaus te bepalen.
 */
class FloodRiskAnalysisService
{
    /**
     * Neerslagdrempels (in mm) per seizoen die waarschuwingen voor overstroomingsrisico activeren.
     */
    private const SEASON_THRESHOLDS_MM = [
        'Winter' => 300,
        'Lente' => 250,
        'Zomer' => 260,
        'Herfst' => 280,
    ];

    /**
     * Maandgroeperingen voor elk seizoen op het noordelijk halfrond.
     *
     * @var array<string, list<int>>
     */
    private const SEASON_MONTHS = [
        'Winter' => [12, 1, 2],
        'Lente' => [3, 4, 5],
        'Zomer' => [6, 7, 8],
        'Herfst' => [9, 10, 11],
    ];

    /**
     * Genereer 5-jaar overstroomingsrisicovoorspelling met trendanalyse.
     * Projecteert neerslag voor de komende 5 jaar op basis van historische patronen en trends.
     * Gebruikt voor lange termijn planning en risicobeoordelingen.
     *
     * @return array<int, array<string, mixed>> Voorspellingsgegevens geïndexeerd per jaar
     */
    public function getFiveYearFloodRiskForecast(): array
    {
        $today = Carbon::now();
        $currentYear = $today->year;
        $nextFiveYears = range($currentYear + 1, $currentYear + 5);

        // Bereken trends uit historische gegevens
        $seasonalTrends = $this->calculateSeasonalTrends();
        $result = [];

        foreach ($nextFiveYears as $forecastYear) {
            $yearsAhead = $forecastYear - $currentYear;
            $result[$forecastYear] = $this->analyzeYearWithTrend($forecastYear, $seasonalTrends, $yearsAhead);
        }

        return $result;
    }

    /**
     * Bereken seizoensgemiddelden uit historische neerslaggegevens (2004-2025).
     * Biedt minimum, maximum en gemiddelde neerslag per seizoen voor basisvergelijking.
     *
     * @return array<string, array<string, float>> Seizoensstatistieken met gemiddelde, min, max
     */
    private function calculateSeasonalAverages(): array
    {
        $neerslag = Neerslag::query()
            ->orderBy('jaar')
            ->orderBy('maand')
            ->get();

        $seasonalTotals = [];

        // Groepeer neerslag per seizoen en jaar
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

        // Bereken statistieken per seizoen
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
     * Bereken seizoenale neerslagtrends met behulp van lineaire regressie.
     * Bepaalt of neerslag toeneemt, afneemt of stabiel blijft in de loop der tijd.
     * Berekent ook variantie (standaarddeviatie) voor natuurlijke jaarlijkse variabiliteit.
     *
     * @return array<string, array<string, mixed>> Trendgegevens met helling, stddev en historische waarden
     */
    private function calculateSeasonalTrends(): array
    {
        $neerslag = Neerslag::query()
            ->orderBy('jaar')
            ->orderBy('maand')
            ->get();

        $seasonalTotals = [];

        // Groepeer neerslag per seizoen en jaar
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

            // Sorteer chronologisch voor trendberekening
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

            // Bereken lineaire regressiehelling (mm per jaar trend)
            $slope = $this->calculateTrendSlope($years, $values);

            // Bereken standaarddeviatie voor variantiemodellering
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
     * Bereken lineaire regressiehelling voor een tijdreeks.
     * Helling vertegenwoordigt de verandering in neerslag per jaar (positief = stijgend, negatief = dalend).
     *
     * @param  array<int>  $years  Array van jaren (x-as)
     * @param  array<float>  $values  Array van neerslagwaarden (y-as)
     * @return float De regressiehelling (mm per jaar)
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
     * Analyseer een bepaald jaar met trendgebaseerde neerslagprojectie.
     * Projecteert seizoenale neerslag en categoriseert risiconiveaus.
     *
     * @param  int  $year  Het jaar om te analyseren
     * @param  array<string, array<string, mixed>>  $trends  Historische trendgegevens
     * @param  int  $yearsAhead  Aantal jaren in de toekomst
     * @return array<string, mixed> Jaaranalyse met seizoensvoorspellingen en totaal risico
     */
    private function analyzeYearWithTrend(int $year, array $trends, int $yearsAhead): array
    {
        $seasons = [];
        $baseYear = Carbon::now()->year;

        foreach (array_keys(self::SEASON_MONTHS) as $season) {
            $trend = $trends[$season];
            $threshold = self::SEASON_THRESHOLDS_MM[$season];

            // Projecteer neerslag gebaseerd op historische trend en variantie
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

        // Tel seizoenen die drempels overschrijden
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
     * Projecteer neerslag voor een toekomstig jaar met behulp van trendextrapolatie en cyclische variantie.
     * Combineert het historische gemiddelde met lange termijn trend en voegt cyclische variabiliteit toe.
     *
     * @param  float  $historicalAverage  Historisch gemiddelde neerslag voor het seizoen
     * @param  float  $trend  De helling uit trendanalyse (mm per jaar)
     * @param  int  $yearsAhead  Aantal jaren in de toekomst
     * @param  float  $stddev  Standaarddeviatie uit historische gegevens
     * @return float Geprojecteerde neerslag in millimeters
     */
    private function projectRainfallForYear(
        float $historicalAverage,
        float $trend,
        int $yearsAhead,
        float $stddev
    ): float {
        // Basisprojectie: pas trend toe op historisch gemiddelde
        $projectedValue = $historicalAverage + ($trend * $yearsAhead);

        // Voeg cyclische variantie toe met sinusfunctie voor deterministisch maar variërend patroon
        // Creëert natuurlijke jaar-tot-jaar oscillatie terwijl deze reproduceerbaar blijft
        $varianceFactor = sin($yearsAhead * 0.5) * ($stddev * 0.3);

        return max($projectedValue + $varianceFactor, 0);
    }

    /**
     * Bepaal overstroomingsrisicokategorie op basis van neerslag versus drempel.
     * Hoog risico: >= 120% van drempel
     * Matig risico: >= 100% van drempel
     * Laag risico: < 100% van drempel
     *
     * @param  float  $rainfall  Geprojecteerde neerslag in millimeters
     * @param  int  $threshold  Seizoensdrempel in millimeters
     * @return 'low'|'medium'|'high' Risicokategorie
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

    /**
     * Wijs een maandnummer toe aan zijn corresponderende seizoen.
     *
     * @param  int  $month  Maandnummer (1-12)
     * @return string|null Seizoensnaam of null als ongeldig
     */
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
     * Analyseer de voortgang van het huidige jaar naar seizoensgebonden overstroomingsdrempels.
     * Vergelijkt voltooide seizoensmaanden en voortgang van de huidige maand tegen drempels.
     * Handig voor real-time controle en waarschuwingen.
     *
     * @return array<string, mixed> Analyse van huidige seizoen met voortgang en risiconiveau
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
