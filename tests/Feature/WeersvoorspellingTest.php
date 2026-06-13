<?php

use App\Models\Materiaal;
use App\Models\Materiaalcategorie;
use App\Models\MateriaalSubcategorie;
use App\Models\Neerslag;
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

test('stockbeheerder can view critical item management page', function () {
    $user = User::factory()->create(['role_id' => \App\Models\Role::STOCKBEHEERDER]);

    $this->actingAs($user)
        ->get(route('weersvoorspelling.kritieke-items'))
        ->assertOk()
        ->assertSee('Beheer Kritieke Items')
        ->assertSee('Kritieke materialen')
        ->assertSee('Snel nieuw materiaal toevoegen');
});

test('critical item management page renders add material subcategory options', function () {
    $user = User::factory()->create(['role_id' => \App\Models\Role::STOCKBEHEERDER]);
    $categorie = Materiaalcategorie::query()->create(['naam' => 'Pompen']);

    MateriaalSubcategorie::query()->create([
        'materiaal_categorie_id' => $categorie->id,
        'naam' => 'Dompelpompen',
    ]);

    $this->actingAs($user)
        ->get(route('weersvoorspelling.kritieke-items'))
        ->assertOk()
        ->assertSee('Pompen')
        ->assertSee('Dompelpompen');
});

test('non stockbeheerder cannot view critical item management page', function () {
    $user = User::factory()->create(['role_id' => \App\Models\Role::TECHNIEKER]);

    $this->actingAs($user)
        ->get(route('weersvoorspelling.kritieke-items'))
        ->assertForbidden();
});

test('stockbeheerder neerslag page does not include critical item management form', function () {
    fakeSuccessfulWeatherApi();

    $user = User::factory()->create(['role_id' => \App\Models\Role::STOCKBEHEERDER]);

    $this->actingAs($user)
        ->get(route('weersvoorspelling'))
        ->assertOk()
        ->assertSee('Actuele neerslag')
        ->assertSee('Start simulatie')
        ->assertDontSee('Kritieke materialen')
        ->assertDontSee('Snel nieuw materiaal toevoegen');
});

test('non stockbeheerder neerslag page does not include simulation control', function () {
    fakeSuccessfulWeatherApi();

    $user = User::factory()->create(['role_id' => \App\Models\Role::TECHNIEKER]);

    $this->actingAs($user)
        ->get(route('weersvoorspelling'))
        ->assertOk()
        ->assertDontSee('Start simulatie')
        ->assertDontSee('Stop simulatie');
});

test('stockbeheerder can update linked critical materials', function () {
    fakeSuccessfulWeatherApi();

    $user = User::factory()->create(['role_id' => \App\Models\Role::STOCKBEHEERDER]);
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
    $user = User::factory()->create(['role_id' => \App\Models\Role::TECHNIEKER]);
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

    $user = User::factory()->create(['role_id' => \App\Models\Role::STOCKBEHEERDER]);

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
    $user = User::factory()->create(['role_id' => \App\Models\Role::TECHNIEKER]);

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

test('stockbeheerder can add neerslag data', function () {
    $user = User::factory()->create(['role_id' => \App\Models\Role::STOCKBEHEERDER]);

    $this->actingAs($user)
        ->post(route('weersvoorspelling.storeNeerslag'), [
            'jaar' => 2024,
            'maand' => 6,
            'mm' => 85,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('neerslags', [
        'jaar' => 2024,
        'maand' => 6,
        'mm' => 85,
    ]);
});

test('stockbeheerder can update existing neerslag data', function () {
    Neerslag::query()->create([
        'jaar' => 2024,
        'maand' => 6,
        'mm' => 75,
    ]);

    $user = User::factory()->create(['role_id' => \App\Models\Role::STOCKBEHEERDER]);

    $this->actingAs($user)
        ->post(route('weersvoorspelling.storeNeerslag'), [
            'jaar' => 2024,
            'maand' => 6,
            'mm' => 85,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('neerslags', [
        'jaar' => 2024,
        'maand' => 6,
        'mm' => 85,
    ]);

    $this->assertDatabaseMissing('neerslags', [
        'jaar' => 2024,
        'maand' => 6,
        'mm' => 75,
    ]);
});

test('non stockbeheerder cannot add neerslag data', function () {
    $user = User::factory()->create(['role_id' => \App\Models\Role::TECHNIEKER]);

    $this->actingAs($user)
        ->post(route('weersvoorspelling.storeNeerslag'), [
            'jaar' => 2024,
            'maand' => 6,
            'mm' => 85,
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('neerslags', [
        'jaar' => 2024,
        'maand' => 6,
    ]);
});

test('neerslag storage validates input', function () {
    $user = User::factory()->create(['role_id' => \App\Models\Role::STOCKBEHEERDER]);

    $this->actingAs($user)
        ->post(route('weersvoorspelling.storeNeerslag'), [
            'jaar' => 2000,
            'maand' => 13,
            'mm' => 2000,
        ])
        ->assertSessionHasErrors(['jaar', 'maand', 'mm']);

    $this->assertDatabaseMissing('neerslags', [
        'jaar' => 2000,
    ]);
});

test('stockbeheerder neerslag page shows add neerslag form', function () {
    fakeSuccessfulWeatherApi();

    $user = User::factory()->create(['role_id' => \App\Models\Role::STOCKBEHEERDER]);

    $this->actingAs($user)
        ->get(route('weersvoorspelling'))
        ->assertOk()
        ->assertSee('Neerslaggegevens toevoegen');
});

test('non stockbeheerder neerslag page does not show add neerslag form', function () {
    fakeSuccessfulWeatherApi();

    $user = User::factory()->create(['role_id' => \App\Models\Role::TECHNIEKER]);

    $this->actingAs($user)
        ->get(route('weersvoorspelling'))
        ->assertOk()
        ->assertDontSee('Neerslaggegevens toevoegen');
});

test('stockbeheerder can see historical neerslag data', function () {
    fakeSuccessfulWeatherApi();

    $user = User::factory()->create(['role_id' => \App\Models\Role::STOCKBEHEERDER]);

    Neerslag::query()->create([
        'jaar' => 2024,
        'maand' => 6,
        'mm' => 85,
    ]);

    $this->actingAs($user)
        ->get(route('weersvoorspelling'))
        ->assertOk()
        ->assertSee('Historische neerslaggegevens')
        ->assertSee('85 mm')
        ->assertSee('2024')
        ->assertSee('Juni');
});

test('empty historical neerslag table shows no data message', function () {
    fakeSuccessfulWeatherApi();

    $user = User::factory()->create(['role_id' => \App\Models\Role::STOCKBEHEERDER]);

    $this->actingAs($user)
        ->get(route('weersvoorspelling'))
        ->assertOk()
        ->assertSee('Historische neerslaggegevens')
        ->assertSee('Geen gegevens beschikbaar');
});
