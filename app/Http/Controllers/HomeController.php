<?php

namespace App\Http\Controllers;

use App\Services\OpenMeteoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const FORECAST_CACHE_KEY = 'home.technieker.rain-forecast';

    public function index(OpenMeteoService $openMeteo): View
    {
        $techniekerRainForecast = [];
        $homeForecastUpdatedAt = null;

        if (auth()->user()?->role === 'technieker') {
            $forecastData = Cache::remember(self::FORECAST_CACHE_KEY, now()->addMinutes(30), function () use ($openMeteo): array {
                return $this->buildTechniekerForecast($openMeteo);
            });

            $techniekerRainForecast = $forecastData['techniekerRainForecast'];
            $homeForecastUpdatedAt = $forecastData['updatedAt'];
        }

        return view('home', [
            'techniekerRainForecast' => $techniekerRainForecast,
            'homeForecastUpdatedAt' => $homeForecastUpdatedAt,
        ]);
    }

    public function refreshForecast(): RedirectResponse
    {
        Cache::forget(self::FORECAST_CACHE_KEY);

        return redirect()
            ->route('home')
            ->with('success', 'Neerslaggegevens vernieuwd.');
    }

    /**
     * @return array{techniekerRainForecast: list<array{day_name: string, amount: float}>, updatedAt: string}
     */
    private function buildTechniekerForecast(OpenMeteoService $openMeteo): array
    {
        $forecast = $openMeteo->fetchForecast(
            OpenMeteoService::DEFAULT_LATITUDE,
            OpenMeteoService::DEFAULT_LONGITUDE,
        );

        if ($forecast === null) {
            return [
                'techniekerRainForecast' => [],
                'updatedAt' => Carbon::now('Europe/Brussels')->format('d-m-Y H:i'),
            ];
        }

        $parsedForecast = $openMeteo->parseForecastForDisplay($forecast);

        return [
            'techniekerRainForecast' => array_slice($parsedForecast['dailyRainForecast'], 0, 7),
            'updatedAt' => Carbon::now('Europe/Brussels')->format('d-m-Y H:i'),
        ];
    }
}
