<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAndLinkMaterialRequest;
use App\Http\Requests\StoreBelangrijkeItemsRequest;
use App\Models\Materiaal;
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
                    'dailyRainForecast' => [],
                    'floodAlarmTriggered' => false,
                    'fiveYearForecast' => $this->floodAnalysis->getFiveYearFloodRiskForecast(),
                    'currentYearAnalysis' => $this->floodAnalysis->getCurrentYearAnalysis(),
                ],
            ));
        }

        $parsed = $this->openMeteo->parseForecastForDisplay($forecast);

        $floodAlarmTriggered = $isSimulated
            ? $this->floodRisk->applySimulation()
            : $this->floodRisk->checkAndFlagItems($latitude, $longitude, $parsed['daily'], $parsed['timezone']);

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
            ],
        ));
    }

    public function storeBelangrijk(StoreBelangrijkeItemsRequest $request): RedirectResponse
    {
        $this->floodRisk->syncLinkedMaterials($request->materiaalIds());

        $this->recalculateFloodRiskFromSession();

        return redirect()
            ->back()
            ->with('success', 'Kritieke materialen succesvol bijgewerkt!');
    }

    public function toggleSimulation(): RedirectResponse
    {
        $wasSimulated = (bool) session('simulate_flood', false);

        session(['simulate_flood' => ! $wasSimulated]);

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
        $materialenByCategory = Materiaal::query()
            ->with('subcategorie.categorie')
            ->orderBy('naam')
            ->get()
            ->groupBy(fn ($m) => $m->subcategorie?->categorie?->naam ?? 'Overig');

        return array_merge([
            'lat' => round($latitude, 2),
            'lon' => round($longitude, 2),
            'alleMaterialen' => $materialenByCategory,
            'gekoppeldeIds' => $linkedIds,
            'isSimulated' => $isSimulated,
            'canManageStock' => auth()->user()?->role === 'stockbeheerder',
        ], $extra);
    }

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
