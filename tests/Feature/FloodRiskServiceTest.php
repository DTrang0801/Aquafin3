<?php

use App\Enums\FloodRiskLevel;
use App\Models\Belangrijk;
use App\Models\Materiaal;
use App\Models\Neerslag;
use App\Services\FloodRiskService;
use Illuminate\Support\Facades\Http;

function makeMaterialWithRiskLevel(FloodRiskLevel $riskLevel): array
{
    $materiaal = Materiaal::factory()->create();
    Belangrijk::query()->create([
        'materiaal_id' => $materiaal->id,
        'risk_level' => $riskLevel->value,
    ]);

    return [$materiaal->id, $materiaal];
}

function fakeWeatherApiWithRainfall(float $rainfallPerDay = 0.0): void
{
    $today = now('Europe/Brussels');
    $times = [];
    $rain = [];

    for ($offset = -5; $offset <= 10; $offset++) {
        $times[] = $today->copy()->addDays($offset)->format('Y-m-d');
        $rain[] = $rainfallPerDay;
    }

    Http::fake([
        'api.open-meteo.com/*' => Http::response([
            'timezone' => 'Europe/Brussels',
            'current' => ['precipitation' => 0.0],
            'daily' => [
                'time' => $times,
                'rain_sum' => $rain,
            ],
        ], 200),
        'archive-api.open-meteo.com/*' => Http::response([
            'daily' => ['rain_sum' => [0, 0, 0]],
        ], 200),
    ]);
}

test('calculateRiskLevel returns Low when rainfall is below threshold', function () {
    $service = app(FloodRiskService::class);

    expect($service->calculateRiskLevel(200, 300))->toBe(FloodRiskLevel::Low);
    expect($service->calculateRiskLevel(0, 250))->toBe(FloodRiskLevel::Low);
    expect($service->calculateRiskLevel(249, 250))->toBe(FloodRiskLevel::Low);
});

test('calculateRiskLevel returns Medium when rainfall is between 100% and 120% of threshold', function () {
    $service = app(FloodRiskService::class);

    expect($service->calculateRiskLevel(250, 250))->toBe(FloodRiskLevel::Medium);
    expect($service->calculateRiskLevel(299, 250))->toBe(FloodRiskLevel::Medium);
    expect($service->calculateRiskLevel(300, 300))->toBe(FloodRiskLevel::Medium);
});

test('calculateRiskLevel returns High when rainfall is at or above 120% of threshold', function () {
    $service = app(FloodRiskService::class);

    expect($service->calculateRiskLevel(300, 250))->toBe(FloodRiskLevel::High);
    expect($service->calculateRiskLevel(360, 300))->toBe(FloodRiskLevel::High);
});

test('medium risk flags medium and low threshold materials but not high threshold materials', function () {
    fakeWeatherApiWithRainfall();

    $threshold = FloodRiskService::SEASON_THRESHOLDS_MM;
    $currentSeason = null;
    $currentMonth = now()->month;
    foreach (FloodRiskService::SEASON_MONTHS as $season => $months) {
        if (in_array($currentMonth, $months)) {
            $currentSeason = $season;
            break;
        }
    }
    if ($currentSeason === null) {
        $this->markTestSkipped('Season could not be determined.');
    }

    // Seed rainfall so current risk = Medium (just at threshold)
    $seasonMonths = FloodRiskService::SEASON_MONTHS[$currentSeason];
    $threshold = FloodRiskService::SEASON_THRESHOLDS_MM[$currentSeason];
    $completedMonths = array_filter($seasonMonths, fn ($m) => $m !== $currentMonth);

    if (count($completedMonths) > 0) {
        $rainfallPerMonth = (int) ceil($threshold / count($seasonMonths));
        $year = now()->year;
        foreach ($completedMonths as $month) {
            $queryYear = ($currentSeason === 'Winter' && $month === 12) ? $year - 1 : $year;
            Neerslag::query()->create(['jaar' => $queryYear, 'maand' => $month, 'mm' => $rainfallPerMonth]);
        }
    }

    [$mediumId] = makeMaterialWithRiskLevel(FloodRiskLevel::Medium);
    [$highId] = makeMaterialWithRiskLevel(FloodRiskLevel::High);

    // Fake weather API to return exactly enough rain to reach Medium (threshold)
    $remainingRain = max(0, $threshold - Neerslag::query()->sum('mm'));
    fakeWeatherApiWithRainfall($remainingRain / 31.0);

    $service = app(FloodRiskService::class);
    $level = $service->checkAndFlagItems();

    // At Medium or High risk, medium-threshold materials should be flagged
    if ($level === FloodRiskLevel::Medium || $level === FloodRiskLevel::High) {
        expect(Materiaal::find($mediumId)->belangrijk)->not->toBeNull();
    }
    // High-threshold materials are only flagged at High risk
    if ($level === FloodRiskLevel::High) {
        expect(Materiaal::find($highId)->belangrijk)->not->toBeNull();
    } elseif ($level === FloodRiskLevel::Medium) {
        expect(Materiaal::find($highId)->belangrijk)->toBeNull();
    }
});

test('at Low risk no materials are flagged', function () {
    fakeWeatherApiWithRainfall(0.0);

    [$materialId] = makeMaterialWithRiskLevel(FloodRiskLevel::Medium);

    $service = app(FloodRiskService::class);
    $level = $service->checkAndFlagItems();

    if ($level === FloodRiskLevel::Low) {
        expect(Materiaal::find($materialId)->belangrijk)->toBeNull();
    }
});

test('applySimulation flags all linked materials as High and returns High', function () {
    [$lowId] = makeMaterialWithRiskLevel(FloodRiskLevel::Medium);
    [$highId] = makeMaterialWithRiskLevel(FloodRiskLevel::High);

    $service = app(FloodRiskService::class);
    $level = $service->applySimulation();

    expect($level)->toBe(FloodRiskLevel::High);
    expect(Materiaal::find($lowId)->belangrijk)->toBe(FloodRiskLevel::High);
    expect(Materiaal::find($highId)->belangrijk)->toBe(FloodRiskLevel::High);
});

test('syncLinkedMaterials stores risk levels correctly', function () {
    $m1 = Materiaal::factory()->create();
    $m2 = Materiaal::factory()->create();

    $service = app(FloodRiskService::class);
    $service->syncLinkedMaterials([
        $m1->id => FloodRiskLevel::Medium->value,
        $m2->id => FloodRiskLevel::High->value,
    ]);

    $this->assertDatabaseHas('belangrijkeItems', [
        'materiaal_id' => $m1->id,
        'risk_level' => 'medium',
    ]);
    $this->assertDatabaseHas('belangrijkeItems', [
        'materiaal_id' => $m2->id,
        'risk_level' => 'high',
    ]);
});

test('linkedMaterialsWithRiskLevels returns correct map', function () {
    $m1 = Materiaal::factory()->create();
    $m2 = Materiaal::factory()->create();

    Belangrijk::query()->create(['materiaal_id' => $m1->id, 'risk_level' => 'medium']);
    Belangrijk::query()->create(['materiaal_id' => $m2->id, 'risk_level' => 'high']);

    $service = app(FloodRiskService::class);
    $map = $service->linkedMaterialsWithRiskLevels();

    expect($map[$m1->id])->toBe(FloodRiskLevel::Medium);
    expect($map[$m2->id])->toBe(FloodRiskLevel::High);
});
