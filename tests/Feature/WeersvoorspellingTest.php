<?php

use App\Models\Materiaal;
use App\Models\MateriaalSubcategorie;
use App\Models\User;
use Illuminate\Support\Facades\Http;

/**
 * @return array<string, mixed>
 */
function fakeOpenMeteoForecastPayload(): array
{
    $today = now('Europe/Brussels');
    $times = [];
    $rain = [];

    for ($offset = -5; $offset <= 10; $offset++) {
        $times[] = $today->copy()->addDays($offset)->format('Y-m-d');
        $rain[] = $offset < 0 ? 2.0 : 0.0;
    }

    return [
        'timezone' => 'Europe/Brussels',
        'current' => ['precipitation' => 1.2],
        'daily' => [
            'time' => $times,
            'rain_sum' => $rain,
        ],
    ];
}

function fakeSuccessfulWeatherApi(): void
{
    Http::fake([
        'api.open-meteo.com/*' => Http::response(fakeOpenMeteoForecastPayload(), 200),
        'archive-api.open-meteo.com/*' => Http::response([
            'daily' => ['rain_sum' => [10, 12, 8]],
        ], 200),
    ]);
}

test('guests are redirected from the neerslag page', function () {
    $this->get(route('weersvoorspelling'))
        ->assertRedirect(route('login'));
});

test('authenticated users can view the neerslag page', function () {
    fakeSuccessfulWeatherApi();

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('weersvoorspelling'))
        ->assertOk()
        ->assertSee('Neerslag')
        ->assertSee('Actuele neerslag');
});

test('stockbeheerder can update linked critical materials', function () {
    fakeSuccessfulWeatherApi();

    $user = User::factory()->create(['role' => 'stockbeheerder']);
    $materiaal = Materiaal::query()->create(['naam' => 'Pomp A']);

    $this->actingAs($user)
        ->post(route('weersvoorspelling.store'), [
            'materiaal_ids' => [$materiaal->id],
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('belangrijkeItems', [
        'materiaal_id' => $materiaal->id,
    ]);
});

test('non stockbeheerders cannot update linked critical materials', function () {
    $user = User::factory()->create(['role' => 'technieker']);
    $materiaal = Materiaal::query()->create(['naam' => 'Pomp B']);

    $this->actingAs($user)
        ->post(route('weersvoorspelling.store'), [
            'materiaal_ids' => [$materiaal->id],
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('belangrijkeItems', [
        'materiaal_id' => $materiaal->id,
    ]);
});

test('neerslag page shows an error when the forecast api fails', function () {
    Http::fake([
        '*' => Http::response('Unavailable', 503),
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('weersvoorspelling'))
        ->assertOk()
        ->assertSee('Kon de neerslaggegevens niet ophalen.');
});

test('stockbeheerder can add a new material and link it as critical', function () {
    fakeSuccessfulWeatherApi();

    $user = User::factory()->create(['role' => 'stockbeheerder']);

    $subcategorie = MateriaalSubcategorie::query()->first();
    if (! $subcategorie) {
        $this->markTestSkipped('No subcategorie available in test database');
    }

    $this->actingAs($user)
        ->post(route('weersvoorspelling.addMaterial'), [
            'naam' => 'Nieuw materiaal',
            'beschrijving' => 'Test beschrijving',
            'materiaal_subcategorie_id' => $subcategorie->id,
            'link_as_critical' => '1',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $materiaal = Materiaal::query()->where('naam', 'Nieuw materiaal')->first();

    $this->assertNotNull($materiaal);
    $this->assertDatabaseHas('belangrijkeItems', [
        'materiaal_id' => $materiaal->id,
    ]);
});

test('non stockbeheerder cannot add a new material', function () {
    $user = User::factory()->create(['role' => 'technieker']);

    $subcategorie = MateriaalSubcategorie::query()->first();
    if (! $subcategorie) {
        $this->markTestSkipped('No subcategorie available in test database');
    }

    $this->actingAs($user)
        ->post(route('weersvoorspelling.addMaterial'), [
            'naam' => 'Nieuw materiaal',
            'beschrijving' => 'Test beschrijving',
            'materiaal_subcategorie_id' => $subcategorie->id,
            'link_as_critical' => '0',
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('materialen', [
        'naam' => 'Nieuw materiaal',
    ]);
});
