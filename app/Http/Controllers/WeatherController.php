<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAndLinkMaterialRequest;
use App\Http\Requests\StoreBelangrijkeItemsRequest;
use App\Http\Requests\StoreNeerslagRequest;
use App\Models\Materiaal;
use App\Models\Neerslag;
use App\Models\Role;
use App\Services\FloodRiskAnalysisService;
use App\Services\FloodRiskService;
use App\Services\OpenMeteoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WeatherController extends Controller
{
    public function __construct(
        private FloodRiskService $floodRisk,
        private OpenMeteoService $openMeteo,
        private FloodRiskAnalysisService $floodAnalysis,
    ) {}

    public function index(Request $request): View
    {
        // Lon en lat ophalen uit de locatie van de gebruiker of standaard locatie (Brussel)
        $coordinates = $this->openMeteo->resolveCoordinates(
            $request->filled('lat') ? $request->float('lat') : null,
            $request->filled('lon') ? $request->float('lon') : null,
        );

        $latitude = $coordinates['lat'];
        $longitude = $coordinates['lon'];

        // Session lat en lon opslaan
        session([
            'weather_latitude' => $latitude,
            'weather_longitude' => $longitude,
        ]);

        // Neerslaggegevens ophalen op basis van lat en lon
        $forecast = $this->openMeteo->fetchForecast($latitude, $longitude);

        // Checken of de simulatiemodus is ingeschakeld
        $isSimulated = (bool) session('simulate_flood', false);
        $linkedIds = $this->floodRisk->linkedMaterialIds();

        // Als de neerslaggegevens niet kunnen worden opgehaald dan een foutmelding tonen en gegevens sturen naar de view.
        if ($forecast === null) {
            return view('pages.weersvoorspelling', $this->baseViewData(
                latitude: $latitude,
                longitude: $longitude,
                linkedIds: $linkedIds,
                isSimulated: $isSimulated,
                extra: [
                    'error' => 'Kon de neerslaggegevens niet ophalen.',
                    'currentRain' => 0,
                    'pastMonthTotal' => 0,
                    'dailyRainForecast' => [],
                    'floodAlarmTriggered' => false,
                    'fiveYearForecast' => $this->floodAnalysis->getFiveYearFloodRiskForecast(),
                    'currentYearAnalysis' => $this->floodAnalysis->getCurrentYearAnalysis(),
                    'historicalNeerslagData' => Neerslag::query()->orderByDesc('jaar')->orderByDesc('maand')->get(),
                ],
            ));
        }

        // Neerslaggegevens parseren
        $parsed = $this->openMeteo->parseForecastForDisplay($forecast);

        // Checken of de simulatiemodus is ingeschakeld
        $floodAlarmTriggered = $isSimulated
            ? $this->floodRisk->applySimulation()
            : $this->floodRisk->checkAndFlagItems($latitude, $longitude, $parsed['daily'], $parsed['timezone']);

        // Gegevens sturen naar de view.
        return view('pages.weersvoorspelling', $this->baseViewData(
            latitude: $latitude,
            longitude: $longitude,
            linkedIds: $linkedIds,
            isSimulated: $isSimulated,
            extra: [
                'currentRain' => $parsed['currentRain'],
                'pastMonthTotal' => $parsed['pastMonthTotal'],
                'dailyRainForecast' => $parsed['dailyRainForecast'],
                'floodAlarmTriggered' => $floodAlarmTriggered,
                'fiveYearForecast' => $this->floodAnalysis->getFiveYearFloodRiskForecast(),
                'currentYearAnalysis' => $this->floodAnalysis->getCurrentYearAnalysis(),
                'historicalNeerslagData' => Neerslag::query()->orderByDesc('jaar')->orderByDesc('maand')->get(),
            ],
        ));
    }

    // Kritieke materialen bijwerken
    public function storeBelangrijk(StoreBelangrijkeItemsRequest $request): RedirectResponse
    {
        // Kritieke materialen bijwerken op basis van de geselecteerde materialen
        $this->floodRisk->syncLinkedMaterials($request->materiaalIds());

        // Neerslaggegevens opnieuw berekenen
        $this->recalculateFloodRiskFromSession();

        return redirect()
            ->back()
            ->with('success', 'Kritieke materialen succesvol bijgewerkt!');
    }

    // Kritieke materialen tonen
    public function criticalItems(): View
    {
        return view('pages.kritieke-items', $this->materialManagementViewData(
            linkedIds: $this->floodRisk->linkedMaterialIds(),
            isSimulated: (bool) session('simulate_flood', false),
        ));
    }

    // Simulatiemodus aan/uit zetten
    public function toggleSimulation(): RedirectResponse
    {
        $wasSimulated = (bool) session('simulate_flood', false);

        session(['simulate_flood' => ! $wasSimulated]);

        $this->recalculateFloodRiskFromSession();

        return redirect()
            ->back()
            ->with('success', 'Simulatiemodus gewijzigd!');
    }

    // Nieuw materiaal toevoegen en koppelen als kritiek item (momenteel niet in gebruik))
    public function addMaterial(CreateAndLinkMaterialRequest $request): RedirectResponse
    {
        $materiaal = Materiaal::query()->create([
            'naam' => $request->string('naam'),
            'beschrijving' => $request->string('beschrijving'),
            'materiaal_subcategorie_id' => $request->integer('materiaal_subcategorie_id'),
            'belangrijk' => false,
        ]);

        if ($request->boolean('link_as_critical')) {
            $this->floodRisk->syncLinkedMaterials(
                array_merge(
                    $this->floodRisk->linkedMaterialIds(),
                    [$materiaal->id],
                ),
            );
        }

        return redirect()
            ->back()
            ->with('success', "Materiaal '{$materiaal->naam}' succesvol toegevoegd!");
    }

    // Neerslaggegevens voor een maand toevoegen
    public function storeNeerslag(StoreNeerslagRequest $request): RedirectResponse
    {
        Neerslag::query()->updateOrCreate(
            [
                'jaar' => $request->integer('jaar'),
                'maand' => $request->integer('maand'),
            ],
            [
                'mm' => $request->integer('mm'),
            ],
        );

        return redirect()
            ->back()
            ->with('success', sprintf('Neerslaggegevens voor %s/%s succesvol opgeslagen!', $request->integer('maand'), $request->integer('jaar')));
    }

    /**
     * @param  list<int>  $linkedIds
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function baseViewData(
        float $latitude,
        float $longitude,
        array $linkedIds,
        bool $isSimulated,
        array $extra = [],
    ): array {
        return array_merge([
            'lat' => round($latitude, 2),
            'lon' => round($longitude, 2),
        ], $this->materialManagementViewData($linkedIds, $isSimulated), $extra);
    }

    /**
     * @param  list<int>  $linkedIds
     * @return array<string, mixed>
     */
    // Lijst met alle materialen sturen naar de view.
    private function materialManagementViewData(array $linkedIds, bool $isSimulated): array
    {
        return [
            'alleMaterialen' => Materiaal::query()
                ->with('subcategorie.categorie')
                ->orderBy('naam')
                ->get()
                ->groupBy(fn (Materiaal $materiaal) => $materiaal->subcategorie?->categorie?->naam ?? 'Overig'),
            'gekoppeldeIds' => $linkedIds,
            'isSimulated' => $isSimulated,
            'canManageStock' => auth()->user()?->role_id === Role::STOCKBEHEERDER,
        ];
    }

    // Neerslaggegevens opnieuw berekenen op basis van de session data
    private function recalculateFloodRiskFromSession(): void
    {
        if (session('simulate_flood', false)) {
            $this->floodRisk->applySimulation();

            return;
        }

        $this->floodRisk->checkAndFlagItems(
            (float) session('weather_latitude', OpenMeteoService::DEFAULT_LATITUDE),
            (float) session('weather_longitude', OpenMeteoService::DEFAULT_LONGITUDE),
        );
    }
}
