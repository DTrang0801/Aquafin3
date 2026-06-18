<?php

use App\Models\Materiaal;
use App\Models\Materiaalcategorie;
use App\Models\MateriaalSubcategorie;
use App\Models\Role;
use App\Models\User;

// Test dat stockbeheerder materialen kan zoeken op naam
test('stockbeheerder kan materialen zoeken op naam', function () {
    $categorie = Materiaalcategorie::create(['naam' => 'Test Categorie']);
    $subcategorie = MateriaalSubcategorie::create([
        'naam' => 'Test Subcategorie',
        'materiaal_categorie_id' => $categorie->id,
    ]);

    Materiaal::create([
        'naam' => 'Waterpomp',
        'materiaal_subcategorie_id' => $subcategorie->id,
        'eenheid' => 'stuk',
    ]);

    Materiaal::create([
        'naam' => 'Hammer',
        'materiaal_subcategorie_id' => $subcategorie->id,
        'eenheid' => 'stuk',
    ]);

    $stockbeheerder = User::factory()->create([
        'role_id' => Role::STOCKBEHEERDER,
    ]);

    // Zoek op "water" - alleen Waterpomp moet gevonden worden
    $response = $this->actingAs($stockbeheerder)
        ->get(route('materialen.beheer', ['zoekterm' => 'water']));

    $response->assertOk();
    $response->assertSee('Waterpomp');
    $response->assertDontSee('Hammer');
});

// Test dat stockbeheerder materialen kan zoeken op beschrijving
test('stockbeheerder kan materialen zoeken op beschrijving', function () {
    $categorie = Materiaalcategorie::create(['naam' => 'Test Categorie']);
    $subcategorie = MateriaalSubcategorie::create([
        'naam' => 'Test Subcategorie',
        'materiaal_categorie_id' => $categorie->id,
    ]);

    Materiaal::create([
        'naam' => 'Materiaal A',
        'beschrijving' => 'Dit is een speciale pomp',
        'materiaal_subcategorie_id' => $subcategorie->id,
        'eenheid' => 'stuk',
    ]);

    Materiaal::create([
        'naam' => 'Materiaal B',
        'beschrijving' => 'Gewoon gereedschap',
        'materiaal_subcategorie_id' => $subcategorie->id,
        'eenheid' => 'stuk',
    ]);

    $stockbeheerder = User::factory()->create([
        'role_id' => Role::STOCKBEHEERDER,
    ]);

    // Zoek op "speciale" - alleen Materiaal A moet gevonden worden
    $response = $this->actingAs($stockbeheerder)
        ->get(route('materialen.beheer', ['zoekterm' => 'speciale']));

    $response->assertOk();
    $response->assertSee('Materiaal A');
    $response->assertDontSee('Materiaal B');
});

// Test dat zoekbalk lege resultaten toont bij geen match
test('zoekbalk toont lege resultaten bij geen match', function () {
    $categorie = Materiaalcategorie::create(['naam' => 'Test Categorie']);
    $subcategorie = MateriaalSubcategorie::create([
        'naam' => 'Test Subcategorie',
        'materiaal_categorie_id' => $categorie->id,
    ]);

    Materiaal::create([
        'naam' => 'Waterpomp',
        'materiaal_subcategorie_id' => $subcategorie->id,
        'eenheid' => 'stuk',
    ]);

    $stockbeheerder = User::factory()->create([
        'role_id' => Role::STOCKBEHEERDER,
    ]);

    // Zoek op iets dat niet bestaat
    $response = $this->actingAs($stockbeheerder)
        ->get(route('materialen.beheer', ['zoekterm' => 'xyz123nietbestaand']));

    $response->assertOk();
    // Controleer dat de zoekterm in de response staat (in het zoekveld)
    $response->assertSee('xyz123nietbestaand', false);
});

// Test dat alle materialen getoond worden zonder zoekterm
test('alle materialen getoond zonder zoekterm', function () {
    $categorie = Materiaalcategorie::create(['naam' => 'Test Categorie']);
    $subcategorie = MateriaalSubcategorie::create([
        'naam' => 'Test Subcategorie',
        'materiaal_categorie_id' => $categorie->id,
    ]);

    Materiaal::create([
        'naam' => 'Waterpomp',
        'materiaal_subcategorie_id' => $subcategorie->id,
        'eenheid' => 'stuk',
    ]);

    Materiaal::create([
        'naam' => 'Hamer',
        'materiaal_subcategorie_id' => $subcategorie->id,
        'eenheid' => 'stuk',
    ]);

    $stockbeheerder = User::factory()->create([
        'role_id' => Role::STOCKBEHEERDER,
    ]);

    // Geen zoekterm - beide materialen moeten getoond worden
    $response = $this->actingAs($stockbeheerder)
        ->get(route('materialen.beheer'));

    $response->assertOk();
    $response->assertSee('Waterpomp');
    $response->assertSee('Hamer');
});
