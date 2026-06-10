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

    public function index(OpenMeteoService $openMeteo): View
    {
        $techniekerRainForecast = []; // standaardwaarden instellen voor niet-techniekers
        $homeForecastUpdatedAt = null;

        if (auth()->user()?->role_id === Role::TECHNIEKER) {
            $forecastData = Cache::remember(self::FORECAST_CACHE_KEY, now()->addMinutes(30), function () use ($openMeteo): array {
                return $this->buildTechniekerForecast($openMeteo); // Forecastgegevens ophalen en cachen voor 30 minuten
            });

            $techniekerRainForecast = $forecastData['techniekerRainForecast'];
            $homeForecastUpdatedAt = $forecastData['updatedAt']; // Tijdstip van de laatste update van de forecast
        }

        return view('home', [
            'techniekerRainForecast' => $techniekerRainForecast, // Neerslagvoorspelling voor techniekers, leeg voor anderen
            'homeForecastUpdatedAt' => $homeForecastUpdatedAt,
        ]);
    }

    public function refreshForecast(): RedirectResponse
    {
        Cache::forget(self::FORECAST_CACHE_KEY); // Verwijder de gecachte forecastgegevens zodat ze bij de geladen homepagina nieuwe automatische verse data ophalen via de API

        return redirect()
            ->route('home')
            ->with('success', 'Neerslaggegevens vernieuwd.');
    }

    // @return array{techniekerRainForecast: list<array{day_name: string, amount: float}>, updatedAt: string}

    private function buildTechniekerForecast(OpenMeteoService $openMeteo): array
    {
        $forecast = $openMeteo->fetchForecast( // Weerdat ophalen voor de standaardlocatie
            OpenMeteoService::DEFAULT_LATITUDE,
            OpenMeteoService::DEFAULT_LONGITUDE,
        );

        if ($forecast === null) {
            return [
                'techniekerRainForecast' => [], // Lege forecast teruggeven als er een fout is bij het ophalen van de gegevens
                'updatedAt' => Carbon::now('Europe/Brussels')->format('d-m-Y H:i'), // Geef toch het huidige tijdstip mee, zodat pagina niet crasht
            ];
        }

        $parsedForecast = $openMeteo->parseForecastForDisplay($forecast); // Zet ruwe API-data naar een nette structuur voor de view

        return [
            'techniekerRainForecast' => array_slice($parsedForecast['dailyRainForecast'], 0, 7), // Alleen de neerslagvoorspelling voor de komende 7 dagen teruggeven
            'updatedAt' => Carbon::now('Europe/Brussels')->format('d-m-Y H:i'), // Sla het huidige tijdstip op in Belgische tijd
        ];
    }
}
