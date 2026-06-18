<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\OpenMeteoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const FORECAST_CACHE_KEY = 'home.technieker.rain-forecast';

    public function index(OpenMeteoService $openMeteo): View|RedirectResponse
    {
        // Stockbeheerders worden direct doorgestuurd naar materiaal beheer
        if (auth()->user()?->role_id === Role::STOCKBEHEERDER) {
            return redirect()->route('materialen.beheer');
        }

        $techniekerRainForecast = [];
        $homeForecastUpdatedAt = null;
        $forecastError = null;

        if (auth()->user()?->role_id === Role::TECHNIEKER) {
            $forecastData = Cache::remember(self::FORECAST_CACHE_KEY, now()->addMinutes(30), function () use ($openMeteo): array {
                return $this->buildTechniekerForecast($openMeteo);
            });

            $techniekerRainForecast = $forecastData['techniekerRainForecast'];
            $homeForecastUpdatedAt = $forecastData['updatedAt'];
            $forecastError = $forecastData['error'] ?? null;
        }

        return view('home', [
            'techniekerRainForecast' => $techniekerRainForecast,
            'homeForecastUpdatedAt' => $homeForecastUpdatedAt,
            'forecastError' => $forecastError,
        ]);
    }

    public function refreshForecast(): RedirectResponse
    {
        Cache::forget(self::FORECAST_CACHE_KEY); // Verwijder de gecachte forecastgegevens zodat ze bij de geladen homepagina nieuwe automatische verse data ophalen via de API

        return redirect()
            ->route('home')
            ->with('success', 'Neerslaggegevens vernieuwd.');
    }

    // @return array{techniekerRainForecast: list<array{day_name: string, amount: float}>, updatedAt: string, error?: string|null}

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
                'error' => 'Kon de neerslaggegevens niet ophalen. De weerservice is momenteel niet beschikbaar.',
            ];
        }

        $parsedForecast = $openMeteo->parseForecastForDisplay($forecast);

        return [
            'techniekerRainForecast' => array_slice($parsedForecast['dailyRainForecast'], 0, 7),
            'updatedAt' => Carbon::now('Europe/Brussels')->format('d-m-Y H:i'),
            'error' => null,
        ];
    }
}
