<?php

namespace App\Http\Controllers;

use App\Enums\FloodRiskLevel;
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
        $coordinates = $this->openMeteo->resolveCoordinates(
            $request->filled('lat') ? $request->float('lat') : null,
            $request->filled('lon') ? $request->float('lon') : null,
        );

        $latitude = $coordinates['lat'];
        $longitude = $coordinates['lon'];

        session([
            'weather_latitude' => $latitude,
            'weather_longitude' => $longitude,
        ]);

        $forecast = $this->openMeteo->fetchForecast($latitude, $longitude);

        $isSimulated = (bool) session('simulate_flood', false);
        $linkedIds = $this->floodRisk->linkedMaterialIds();

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
                    'pastThreeMonthsTotal' => 0,
                    'dailyRainForecast' => [],
                    'currentRiskLevel' => FloodRiskLevel::Low,
                    'riskPercentage' => 0,
                    'floodAlarmTriggered' => false,
                    'fiveYearForecast' => $this->floodAnalysis->getFiveYearFloodRiskForecast(),
                    'currentYearAnalysis' => $this->floodAnalysis->getCurrentYearAnalysis(),
                    'historicalNeerslagData' => Neerslag::query()->orderByDesc('jaar')->orderByDesc('maand')->get(),
                ],
            ));
        }

        $parsed = $this->openMeteo->parseForecastForDisplay($forecast);

        $currentRiskLevel = $isSimulated
            ? $this->floodRisk->applySimulation(session('simulate_level'))
            : $this->floodRisk->checkAndFlagItems($latitude, $longitude, $parsed['daily'], $parsed['timezone']);

        $riskPercentage = $this->floodRisk->calculateRiskPercentage(
            $latitude,
            $longitude,
            $parsed['daily'],
            $parsed['timezone'],
        );

        return view('pages.weersvoorspelling', $this->baseViewData(
            latitude: $latitude,
            longitude: $longitude,
            linkedIds: $linkedIds,
            isSimulated: $isSimulated,
            extra: [
                'currentRain' => $parsed['currentRain'],
                'pastMonthTotal' => $parsed['pastMonthTotal'],
                'pastThreeMonthsTotal' => $parsed['pastThreeMonthsTotal'],
                'dailyRainForecast' => $parsed['dailyRainForecast'],
                'currentRiskLevel' => $currentRiskLevel,
                'riskPercentage' => $riskPercentage,
                'floodAlarmTriggered' => $currentRiskLevel !== FloodRiskLevel::Low,
                'fiveYearForecast' => $this->floodAnalysis->getFiveYearFloodRiskForecast(),
                'currentYearAnalysis' => $this->floodAnalysis->getCurrentYearAnalysis(),
                'historicalNeerslagData' => Neerslag::query()->orderByDesc('jaar')->orderByDesc('maand')->get(),
            ],
        ));
    }

    public function storeBelangrijk(StoreBelangrijkeItemsRequest $request): RedirectResponse
    {
        $this->floodRisk->syncLinkedMaterials($request->materialRiskLevels());

        $this->recalculateFloodRiskFromSession();

        return redirect()
            ->back()
            ->with('success', 'Kritieke materialen succesvol bijgewerkt!');
    }

    public function criticalItems(): View
    {
        return view('pages.kritieke-items', array_merge(
            $this->materialManagementViewData(
                linkedIds: $this->floodRisk->linkedMaterialIds(),
                isSimulated: (bool) session('simulate_flood', false),
            ),
            [
                'gekoppeldeRiskLevels' => $this->floodRisk->linkedMaterialsWithRiskLevels(),
                'riskLevelOptions' => FloodRiskLevel::cases(),
            ]
        ));
    }

    public function toggleSimulation(Request $request): RedirectResponse
    {
        $level = $request->string('level', 'none');

        if ($level === 'none') {
            session(['simulate_flood' => false, 'simulate_level' => null]);
        } else {
            session(['simulate_flood' => true, 'simulate_level' => $level]);
        }

        $this->recalculateFloodRiskFromSession();

        return redirect()
            ->back()
            ->with('success', 'Simulatiemodus gewijzigd!');
    }

    public function addMaterial(CreateAndLinkMaterialRequest $request): RedirectResponse
    {
        $materiaal = Materiaal::query()->create([
            'naam' => $request->string('naam'),
            'beschrijving' => $request->string('beschrijving'),
            'materiaal_subcategorie_id' => $request->integer('materiaal_subcategorie_id'),
            'belangrijk' => false,
        ]);

        if ($request->boolean('link_as_critical')) {
            $current = $this->floodRisk->linkedMaterialsWithRiskLevels();
            $current[$materiaal->id] = FloodRiskLevel::Medium->value;
            $this->floodRisk->syncLinkedMaterials($current);
        }

        return redirect()
            ->back()
            ->with('success', "Materiaal '{$materiaal->naam}' succesvol toegevoegd!");
    }

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

    private function recalculateFloodRiskFromSession(): void
    {
        if (session('simulate_flood', false)) {
            $this->floodRisk->applySimulation(session('simulate_level'));

            return;
        }

        $this->floodRisk->checkAndFlagItems(
            (float) session('weather_latitude', OpenMeteoService::DEFAULT_LATITUDE),
            (float) session('weather_longitude', OpenMeteoService::DEFAULT_LONGITUDE),
        );
    }
}
