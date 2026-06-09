<?php

namespace App\Services;

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
     * Als seizoensgebonden neerslag deze drempels overschrijdt, worden materialen gemarkeerd als belangrijk.
     */
    private const SEASON_THRESHOLDS_MM = [
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
    private const SEASON_MONTHS = [
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
     * @param  int  $year  
     * @param  int  $month  
     * @param  float  $latitude   (standaard naar Brussel)
     * @param  float  $longitude   (standaard naar Brussel)
     */
    public function archiveHistoricalMonth(
        int $year,
        int $month,
        float $latitude = OpenMeteoService::DEFAULT_LATITUDE,
        float $longitude = OpenMeteoService::DEFAULT_LONGITUDE,
    ): void {
        // Sla over als gegevens voor deze maand al bestaan
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

        // Sla afgeronde maandelijkse totale neerslag op
        Neerslag::query()->create([
            'jaar' => $year,
            'maand' => $month,
            'mm' => (int) round($monthlyTotal),
        ]);
    }

    /**
     * Evalueer huidige overstroomingsrisico en markeer gekoppelde materialen.
     *
     * @param  float  $latitude  Locatiebreedte (standaard naar Brussel)
     * @param  float  $longitude  Locatielengte (standaard naar Brussel)
     * @param  array<string, mixed>|null  $forecastDaily  Optionele voorspellingsgegevens om dubbele API-aanroepen te vermijden
     * @param  string  $timezone  Tijdzone voor datumberekeningen
     * @return bool Waar als overstroomingsrisico momenteel actief is
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

    /**
     * Simuleer overstroomingsrisico door alle gekoppelde materialen als belangrijk in te stellen.
     * Gebruikt voor test- en demonstratiedoeleinden.
     *
     * @return bool Geeft altijd waar terug (simulatie is actief)
     */
    public function applySimulation(): bool
    {
        $this->applyMaterialFlags(true);

        return true;
    }

    /**
     * Synchroniseer gekoppelde materialen met de Belangrijk-tabel.
     * Wist bestaande koppelingen en maakt nieuwe aan op basis van verstrekte ID's.
     * Dit stelt beheerders in staat om in te stellen welke materialen worden gecontroleerd bij risicobeoordelingen van overstromingen.
     *
     * @param  list<int>  $materiaalIds  Array van materiaal-ID's om aan te koppelen voor controle van overstroomingsrisico
     */
    public function syncLinkedMaterials(array $materiaalIds): void
    {
        // Wis bestaande koppelingen
        Belangrijk::query()->delete();

        if ($materiaalIds === []) {
            return;
        }

        $now = now();

        // Maak nieuwe koppelingen met tijdstempels
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
     * Evalueer overstroomingsrisico voor het huidige seizoen.
     * Combineert historische seizoensgebonden neerslag met voorspelde neerslag om te bepalen of drempels worden overschreden.
     *
     * @param  float  $latitude  Locatiebreedte
     * @param  float  $longitude  Locatielengte
     * @param  array<string, mixed>|null  $forecastDaily  Optionele voorspellingsgegevens
     * @param  string  $timezone  Tijdzone voor berekeningen
     * @return bool Waar als opgehoopte neerslag de seizoendrempel overschrijdt
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

        // Verkrijg opgehoopte neerslag uit vorige maanden in dit seizoen
        $totalRainfall = $this->accumulatedSeasonRainfall(
            $currentSeason,
            $currentMonth,
            $currentYear,
        );

        // Haal voorspelling op als deze niet is opgegeven
        if ($forecastDaily === null) {
            $forecast = $this->openMeteo->fetchForecast($latitude, $longitude);
            $forecastDaily = $forecast['daily'] ?? null;
            $timezone = $forecast['timezone'] ?? $timezone;
        }

        // Voeg verwachte neerslag voor de huidige maand toe
        $totalRainfall += $this->openMeteo->sumRainfallForMonth($forecastDaily, $currentMonth, $timezone);

        // Controleer of totaal seizoendrempel overschrijdt
        return $totalRainfall >= self::SEASON_THRESHOLDS_MM[$currentSeason];
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

            // Behandel het Winterseizoen dat de jaargrens kruist (December is vorig jaar)
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
     * Pas overstroomingsrisicomarkeringen toe op materialen in het magazijn.
     * Werkt het veld "belangrijk" bij op basis van of materialen zijn gekoppeld voor controle
     * en of overstroomingsrisico momenteel actief is.
     *
     * @param  bool  $isFloodRiskActive  Of overstroomingsrisicoaandoeningen momenteel aanwezig zijn
     */
    private function applyMaterialFlags(bool $isFloodRiskActive): void
    {
        $linkedIds = $this->linkedMaterialIds();

        if ($linkedIds === []) {
            // Als geen materialen zijn gekoppeld, wis alle markeringen
            Materiaal::query()->update(['belangrijk' => false]);

            return;
        }

        // Markeer alleen gekoppelde materialen op basis van overstroomingsrisicostatus
        Materiaal::query()
            ->whereIn('id', $linkedIds)
            ->update(['belangrijk' => $isFloodRiskActive]);

        // Verwijder markeringen van alle niet-gekoppelde materialen
        Materiaal::query()
            ->whereNotIn('id', $linkedIds)
            ->update(['belangrijk' => false]);
    }
}
