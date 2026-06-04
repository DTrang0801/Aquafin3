<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    protected $floodService;

    public function __construct(FloodRiskService $floodService)
    {
        $this->floodService = $floodService;
    }

    public function index(Request $request)
    {
        $lastMonth = Carbon::now('Europe/Berlin')->subMonth();
        $this->floodService->archiveHistoricalMonth($lastMonth->year, $lastMonth->month);

        // Read coordinates from the request, default to Belgium
        $lat = $request->input('lat', 50.8503);
        $lon = $request->input('lon', 4.3517);

        // 1. Establish defaults up front to prevent Undefined Variable exceptions downstream
        $isSimulated = session('simulate_flood', false);
        $floodAlarmTriggered = false;

        // 2. Fetch API Data safely
        $response = Http::withoutVerifying() // bypass local TLS/cURL handshake crashes
            ->withOptions([
                'curl' => [
                    CURLOPT_CONNECTTIMEOUT => 10,
                ],
            ])->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $lat,
                'longitude' => $lon,
                'daily' => 'rain_sum',
                'current' => 'rain,precipitation',
                'timezone' => 'Europe/Berlin',
                'past_days' => 30,
                'forecast_days' => 14,
            ]);

        // Data structures initialization
        $pastMonthTotal = 0;
        $dailyRainForecast = [];
        $currentRain = 0;
        $error = null;

        if ($response->failed()) {
            $error = 'Kon de neerslaggegevens niet ophalen.';
        } else {
            $data = $response->json();
            $current = $data['current'] ?? null;
            $daily = $data['daily'] ?? null;
            $currentRain = $current['precipitation'] ?? 0;

            $detectedTimezone = $data['timezone'] ?? 'Europe/Berlin';

            if ($daily && isset($daily['time'])) {
                $today = Carbon::today($detectedTimezone);

                foreach ($daily['time'] as $index => $dateString) {
                    $date = Carbon::parse($dateString);
                    $amount = $daily['rain_sum'][$index] ?? 0;

                    if ($date->isBefore($today)) {
                        $pastMonthTotal += $amount;
                    } else {
                        $dailyRainForecast[] = [
                            'day_name' => $date->locale('nl')->isoFormat('dddd D MMM'),
                            'amount' => $amount,
                        ];
                    }
                }
            }
        }

        // 3. Process Flood Calculation Alerts (Runs even if API drops out)
        if ($isSimulated) {
            // Force code to act as if flood risk threshold is breached
            $floodAlarmTriggered = true;

            // Re-apply the database updates using forced TRUE state
            $floodMaterialIds = DB::table('belangrijkeItems')->pluck('materiaal_id')->toArray();
            if (! empty($floodMaterialIds)) {
                DB::table('materialen')->whereIn('id', $floodMaterialIds)->update(['belangrijk' => true]);
            }
            DB::table('materialen')->whereNotIn('id', $floodMaterialIds)->update(['belangrijk' => false]);
        } else {
            // Run normal calculation logic based on live Open-Meteo metrics
            $floodAlarmTriggered = $this->floodService->checkAndFlagItems($lat, $lon);
        }

        $alleMaterialen = DB::table('materialen')->select('id', 'naam')->get();
        $gekoppeldeIds = DB::table('belangrijkeItems')->pluck('materiaal_id')->toArray();

        // 4. Return a clean unified response payload configuration
        return view('pages.weersvoorspelling', [
            'currentRain' => $currentRain,
            'pastMonthTotal' => round($pastMonthTotal, 1),
            'dailyRainForecast' => $dailyRainForecast,
            'lat' => round($lat, 2),
            'lon' => round($lon, 2),
            'floodAlarm' => $floodAlarmTriggered,
            'alleMaterialen' => $alleMaterialen,
            'gekoppeldeIds' => $gekoppeldeIds,
            'floodAlarmTriggered' => $floodAlarmTriggered, // Now guaranteed to exist!
            'isSimulated' => $isSimulated,
            'error' => $error,
        ]);
    }

    public function storeBelangrijk(Request $request)
    {
        // Get checked IDs from the form checkboxes
        $geselecteerdeIds = $request->input('materiaal_ids', []);

        // Clear out old relationships to prevent duplicates
        DB::table('belangrijkeItems')->truncate();

        // Re-insert new pairs safely
        $rows = [];
        $now = now();
        foreach ($geselecteerdeIds as $id) {
            $rows[] = [
                'materiaal_id' => (int) $id,
            ];
        }

        if (! empty($rows)) {
            DB::table('belangrijkeItems')->insert($rows);
        }

        // Run calculations immediately so the database syncs with current weather status
        $this->floodService->checkAndFlagItems(50.75, 4.5);

        return redirect()->back()->with('success', 'Kritieke materialen succesvol bijgewerkt!');
    }

    public function toggleSimulation()
    {
        $currentState = session('simulate_flood', false);

        // Invert the state
        session(['simulate_flood' => ! $currentState]);

        // If turning simulation off, force a recalculation immediately to restore live data state
        if ($currentState === true) {
            $this->floodService->checkAndFlagItems(50.75, 4.5);
        }

        return redirect()->back()->with('success', 'Simulatiemodus gewijzigd!');
    }
}
