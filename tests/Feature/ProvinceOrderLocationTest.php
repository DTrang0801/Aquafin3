<?php

use App\Enums\Province;
use App\Models\Bestelling;
use App\Models\Mandje;
use App\Models\Materiaal;
use App\Models\Materiaalcategorie;
use App\Models\MateriaalSubcategorie;
use App\Models\Role;
use App\Models\User;

test('technician with province gets depot location on order', function () {
    $categorie = Materiaalcategorie::create(['naam' => 'Test Categorie']);
    $subcategorie = MateriaalSubcategorie::create([
        'naam' => 'Test Subcategorie',
        'materiaal_categorie_id' => $categorie->id,
    ]);

    $materiaal = Materiaal::create([
        'naam' => 'Test Material',
        'materiaal_subcategorie_id' => $subcategorie->id,
        'eenheid' => 'stuk',
    ]);

    $technician = User::factory()->create([
        'role_id' => Role::TECHNIEKER,
        'province' => Province::Antwerpen->value,
    ]);

    $mandje = Mandje::create(['gebruiker_id' => $technician->id]);
    $mandje->materialen()->attach($materiaal->id, ['aantal' => 2]);

    $response = $this->actingAs($technician)->post(route('winkelmandje.confirm'), [
        'gevraagde_datum' => now()->addDay()->toDateString(),
        'gevraagde_tijd' => '09:00',
        'use_custom_location' => false,
    ]);

    $response->assertRedirect(route('bestellingen'));

    $bestelling = Bestelling::latest()->first();
    expect($bestelling->locatie)->toBe('Depot Antwerpen, Antwerpen');
    expect($bestelling->custom_location_used)->toBeFalse();
});

test('technician can override with custom location', function () {
    $categorie = Materiaalcategorie::create(['naam' => 'Test Categorie']);
    $subcategorie = MateriaalSubcategorie::create([
        'naam' => 'Test Subcategorie',
        'materiaal_categorie_id' => $categorie->id,
    ]);

    $materiaal = Materiaal::create([
        'naam' => 'Test Material',
        'materiaal_subcategorie_id' => $subcategorie->id,
        'eenheid' => 'stuk',
    ]);

    $technician = User::factory()->create([
        'role_id' => Role::TECHNIEKER,
        'province' => Province::VlaamsBrabant->value,
    ]);

    $mandje = Mandje::create(['gebruiker_id' => $technician->id]);
    $mandje->materialen()->attach($materiaal->id, ['aantal' => 1]);

    $customLocation = 'Werf Brussel Noord';

    $response = $this->actingAs($technician)->post(route('winkelmandje.confirm'), [
        'gevraagde_datum' => now()->addDay()->toDateString(),
        'gevraagde_tijd' => '09:00',
        'use_custom_location' => true,
        'locatie' => $customLocation,
    ]);

    $response->assertRedirect(route('bestellingen'));

    $bestelling = Bestelling::latest()->first();
    expect($bestelling->locatie)->toBe($customLocation);
    expect($bestelling->custom_location_used)->toBeTrue();
});

test('all provinces map to correct depot addresses', function () {
    expect(Province::VlaamsBrabant->getDepotAddress())->toBe('Depot Vlaams-Brabant');
    expect(Province::WestVlaanderen->getDepotAddress())->toBe('Depot West-Vlaanderen');
    expect(Province::OostVlaanderen->getDepotAddress())->toBe('Depot Oost-Vlaanderen');
    expect(Province::Limburg->getDepotAddress())->toBe('Depot Limburg');
    expect(Province::Antwerpen->getDepotAddress())->toBe('Depot Antwerpen');
});

test('user without province gets depot location null', function () {
    $technician = User::factory()->create([
        'role_id' => Role::TECHNIEKER,
        'province' => null,
    ]);

    expect($technician->getDepotLocation())->toBeNull();
});
