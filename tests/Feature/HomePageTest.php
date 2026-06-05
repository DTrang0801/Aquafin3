<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

/**
 * @return array<string, mixed>
 */
function fakeHomeForecastPayload(int $startingAmount = 1): array
{
    $times = [];
    $rain = [];

    for ($offset = 0; $offset < 10; $offset++) {
        $times[] = now('Europe/Brussels')->copy()->addDays($offset)->format('Y-m-d');
        $rain[] = (float) ($startingAmount + $offset);
    }

    return [
        'timezone' => 'Europe/Brussels',
        'current' => ['precipitation' => 0.0],
        'daily' => [
            'time' => $times,
            'rain_sum' => $rain,
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function fakeHomeForecastPayloadWithAmount(float $amount): array
{
    $times = [];
    $rain = [];

    for ($offset = 0; $offset < 10; $offset++) {
        $times[] = now('Europe/Brussels')->copy()->addDays($offset)->format('Y-m-d');
        $rain[] = $amount;
    }

    return [
        'timezone' => 'Europe/Brussels',
        'current' => ['precipitation' => 0.0],
        'daily' => [
            'time' => $times,
            'rain_sum' => $rain,
        ],
    ];
}

test('technieker sees a seven day rain forecast on the landing page', function () {
    Http::fake([
        'api.open-meteo.com/*' => Http::response(fakeHomeForecastPayload(), 200),
    ]);

    $user = User::factory()->create(['role' => 'technieker']);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('7-daagse neerslagverwachting')
        ->assertSee('Volledige voorspelling')
        ->assertSee('Data verversen')
        ->assertSee('1.0 mm')
        ->assertSee('7.0 mm')
        ->assertDontSee('8.0 mm');
});

test('technieker landing page forecast is cached until refreshed', function () {
    Http::fake([
        'api.open-meteo.com/*' => Http::sequence()
            ->push(fakeHomeForecastPayloadWithAmount(1.5), 200)
            ->push(fakeHomeForecastPayloadWithAmount(9.5), 200),
    ]);

    $user = User::factory()->create(['role' => 'technieker']);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('1.5 mm')
        ->assertDontSee('9.5 mm');

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('1.5 mm')
        ->assertDontSee('9.5 mm');

    Http::assertSentCount(1);
});

test('technieker can refresh the cached forecast', function () {
    Http::fake([
        'api.open-meteo.com/*' => Http::sequence()
            ->push(fakeHomeForecastPayloadWithAmount(1.5), 200)
            ->push(fakeHomeForecastPayloadWithAmount(9.5), 200),
    ]);

    $user = User::factory()->create(['role' => 'technieker']);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('1.5 mm');

    $this->actingAs($user)
        ->post(route('home.forecast.refresh'))
        ->assertRedirect(route('home'))
        ->assertSessionHas('success', 'Neerslaggegevens vernieuwd.');

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('9.5 mm');

    Http::assertSentCount(2);
});

test('non techniekers do not see the landing page rain forecast', function () {
    $user = User::factory()->create(['role' => 'stockbeheerder']);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertDontSee('7-daagse neerslagverwachting')
        ->assertDontSee('Volledige voorspelling');
});
