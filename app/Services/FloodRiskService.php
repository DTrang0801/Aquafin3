<?php

namespace App\Services;

use App\Enums\FloodRiskLevel;
use App\Models\Belangrijk;
use App\Models\Materiaal;
use App\Models\Neerslag;
use Illuminate\Support\Carbon;

/**
 * Service voor het beheren van risicobeoordelingen en beheer van materialen.
 * Bepaalt overstroomingsrisico op basis van neerslagvoorspellingen en historische gegevens,
 * markeert belangrijke materialen wanneer overstroomingsrisico actief is, en archiveert historische neerslaggegevens.
 *
 * Overstroomingsrisico wordt berekend door seizoensgebonden opgehoopte neerslag te vergelijken met voorgedefinieerde drempels.
 * Wanneer risico wordt gedetecteerd, worden gekoppelde materialen gemarkeerd als "belangrijk" voor magazijnbeheer.
 */
class FloodRiskService
{
    /**
     * Neerslagdrempels (in mm) per seizoen die overstroomingsrisico aangeven.
     * Gedeeld met FloodRiskAnalysisService voor consistente drempelwaarden.
     */
    public const SEASON_THRESHOLDS_MM = [
        'Winter' => 300,
        'Lente' => 250,
        'Zomer' => 260,
        'Herfst' => 280,
    ];

    /**
     * Maandtoewijzingen voor elk seizoen op het noordelijk halfrond.
     *
     * @var array<string, list<int>>
     */
    public const SEASON_MONTHS = [
        'Winter' => [12, 1, 2],
        'Lente' => [3, 4, 5],
        'Zomer' => [6, 7, 8],
        'Herfst' => [9, 10, 11],
    ];

    public function __construct(private OpenMeteoService $openMeteo) {}

    /**
     * Archiveer maandelijkse neerslaggegevens van Open-Meteo.
     * Voorkomt dubbele vermeldingen en vult de Neerslag-tabel met eerdere weergegevens.
     * Aangeroepen door de ArchivePastMonth-opdracht om een historische dataset samen te stellen.
     *
     * @param  float  $latitude  (standaard naar Brussel)
     * @param  float  $longitude  (standaard naar Brussel)
     */
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
     * Evalueer huidig overstroomingsrisico en markeer gekoppelde materialen op basis van hun risicodniveau.
     * Materialen worden alleen gemarkeerd als het actuele risico hun minimale drempel bereikt of overschrijdt.
     *
     * @param  float  $latitude  Locatiebreedte (standaard naar Brussel)
     * @param  float  $longitude  Locatielengte (standaard naar Brussel)
     * @param  array<string, mixed>|null  $forecastDaily  Optionele voorspellingsgegevens om dubbele API-aanroepen te vermijden
     * @param  string  $timezone  Tijdzone voor datumberekeningen
     * @return FloodRiskLevel Huidige graduele risicostatus
     */
    public function checkAndFlagItems(
        float $latitude = OpenMeteoService::DEFAULT_LATITUDE,
        float $longitude = OpenMeteoService::DEFAULT_LONGITUDE,
        ?array $forecastDaily = null,
        string $timezone = 'Europe/Berlin',
    ): FloodRiskLevel {
        $currentLevel = $this->evaluateFloodRisk($latitude, $longitude, $forecastDaily, $timezone);
        $this->applyMaterialFlags($currentLevel);

        return $currentLevel;
    }

    /**
     * Simuleer overstroomingsrisico door alle gekoppelde materialen te markeren met het opgegeven risiconiveau.
     * Gebruikt voor test- en demonstratiedoeleinden.
     *
     * @param  FloodRiskLevel|string|null  $level  Het risiconiveau om te simuleren (FloodRiskLevel, string, of null)
     * @return FloodRiskLevel|null Het gesimuleerde risiconiveau of null als simulatie is uitgeschakeld
     */
    public function applySimulation(FloodRiskLevel|string|null $level = null): ?FloodRiskLevel
    {
        if ($level === null || (is_string($level) && $level === 'none')) {
            // Clear simulation
            $this->applyMaterialFlags(FloodRiskLevel::Low);

            return null;
        }

        $simulationLevel = is_string($level) ? FloodRiskLevel::from($level) : $level;
        $this->applyMaterialFlags($simulationLevel);

        return $simulationLevel;
    }

    /**
     * Synchroniseer gekoppelde materialen met de Belangrijk-tabel inclusief hun risicodniveaus.
     * Wist bestaande koppelingen en maakt nieuwe aan op basis van verstrekte ID's en risiconiveaus.
     *
     * @param  array<int|string, string>  $materialRiskLevels  Associatieve array van materiaal_id => risk_level string
     */
    public function syncLinkedMaterials(array $materialRiskLevels): void
    {
        Belangrijk::query()->delete();

        if ($materialRiskLevels === []) {
            return;
        }

        $now = now();

        Belangrijk::query()->insert(
            collect($materialRiskLevels)
                ->map(fn (string $riskLevel, int|string $id): array => [
                    'materiaal_id' => (int) $id,
                    'risk_level' => $riskLevel,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
                ->values()
                ->all()
        );
    }

    /**
     * Verkrijg ID's van alle materialen die momenteel zijn gekoppeld voor controle van overstroomingsrisico.
     *
     * @return list<int> Array van materiaal-ID's
     */
    public function linkedMaterialIds(): array
    {
        return Belangrijk::query()
            ->pluck('materiaal_id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * Verkrijg gekoppelde materialen als een map van materiaal-ID naar risiconiveau.
     *
     * @return array<int, FloodRiskLevel> Associatieve array van materiaal_id => FloodRiskLevel
     */
    public function linkedMaterialsWithRiskLevels(): array
    {
        return Belangrijk::query()
            ->get()
            ->mapWithKeys(fn (Belangrijk $item): array => [
                $item->materiaal_id => $item->risk_level ?? FloodRiskLevel::Medium,
            ])
            ->all();
    }

    /**
     * Evalueer het graduele overstroomingsrisico voor het huidige seizoen.
     * Combineert historische seizoensgebonden neerslag met voorspelde neerslag.
     *
     * Risicobepaling:
     * - Low:    < 100 % van seizoensdrempel
     * - Medium: >= 100 % en < 120 % van seizoensdrempel
     * - High:   >= 120 % van seizoensdrempel
     *
     * @param  float  $latitude  Locatiebreedte
     * @param  float  $longitude  Locatielengte
     * @param  array<string, mixed>|null  $forecastDaily  Optionele voorspellingsgegevens
     * @param  string  $timezone  Tijdzone voor berekeningen
     */
    private function evaluateFloodRisk(
        float $latitude,
        float $longitude,
        ?array $forecastDaily = null,
        string $timezone = 'Europe/Berlin',
    ): FloodRiskLevel {
        $now = Carbon::now('Europe/Berlin');
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $currentSeason = $this->seasonForMonth($currentMonth);

        if ($currentSeason === null) {
            return FloodRiskLevel::Low;
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

        return $this->calculateRiskLevel($totalRainfall, self::SEASON_THRESHOLDS_MM[$currentSeason]);
    }

    /**
     * Bepaal het graduele risiconiveau op basis van neerslag versus drempel.
     * Gedeelde logica met FloodRiskAnalysisService.
     *
     * @param  float  $rainfall  Opgehoopte neerslag in millimeters
     * @param  int  $threshold  Seizoensdrempel in millimeters
     */
    public function calculateRiskLevel(float $rainfall, int $threshold): FloodRiskLevel
    {
        if ($rainfall >= $threshold * 1.2) {
            return FloodRiskLevel::High;
        }

        if ($rainfall >= $threshold) {
            return FloodRiskLevel::Medium;
        }

        return FloodRiskLevel::Low;
    }

    /**
     * Bereken opgehoopte neerslag voor voltooide maanden in het huidige seizoen.
     * Sluit de huidige maand uit (die wordt afgehandeld door voorspellingsgegevens).
     *
     * @param  string  $season  Seizoensnaam (Winter, Lente, Zomer, Herfst)
     * @param  int  $currentMonth  Huidge maandnummer (1-12)
     * @param  int  $currentYear  Huidig jaar
     * @return float Totale neerslag in millimeters voor voltooide seizoensmaanden
     */
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

    /**
     * Wijs een maandnummer toe aan zijn corresponderende seizoen.
     *
     * @param  int  $month  Maandnummer (1-12)
     * @return string|null Seizoensnaam of null als ongeldig maandnummer
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
     * Pas graduele overstroomingsrisicomarkeringen toe op materialen in het magazijn.
     * Een materiaal wordt gemarkeerd als het actuele risiconiveau het minimale drempelniveau
     * van dat materiaal bereikt of overschrijdt. Bij laag risico worden geen materialen gemarkeerd.
     *
     * @param  FloodRiskLevel  $currentLevel  Huidig gradueel risiconiveau
     */
    private function applyMaterialFlags(FloodRiskLevel $currentLevel): void
    {
        $linkedItems = Belangrijk::query()->get();

        if ($linkedItems->isEmpty()) {
            Materiaal::query()->update(['belangrijk' => null]);

            return;
        }

        $linkedIds = $linkedItems->pluck('materiaal_id')->map(fn ($id): int => (int) $id)->all();

        // Clear non-linked materials
        Materiaal::query()
            ->whereNotIn('id', $linkedIds)
            ->update(['belangrijk' => null]);

        // At Low risk nothing gets flagged; clear all linked materials too
        if ($currentLevel === FloodRiskLevel::Low) {
            Materiaal::query()
                ->whereIn('id', $linkedIds)
                ->update(['belangrijk' => null]);

            return;
        }

        // Flag each linked material based on whether the current risk meets its threshold
        foreach ($linkedItems as $item) {
            $requiredLevel = $item->risk_level ?? FloodRiskLevel::Medium;
            $isFlagged = $currentLevel->meetsOrExceeds($requiredLevel);

            Materiaal::query()
                ->where('id', $item->materiaal_id)
                ->update(['belangrijk' => $isFlagged ? $currentLevel->value : null]);
        }
    }
}
