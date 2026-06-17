<?php

use App\Models\Mandje;
use App\Models\Materiaal;
use App\Models\Materiaalcategorie;
use App\Models\MateriaalSubcategorie;
use App\Models\Role;
use App\Models\User;

// Test dat een technicus de hoeveelheid in het mandje kan bijwerken via AJAX
test('technician can update cart quantity via ajax', function () {
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
    ]);

    $mandje = Mandje::create(['gebruiker_id' => $technician->id]);
    $mandje->materialen()->attach($materiaal->id, ['aantal' => 2]);

    $response = $this->actingAs($technician)
        ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
        ->patch(route('winkelmandje.update', $materiaal->id), [
            'aantal' => 5,
        ]);

    $response->assertOk()
        ->assertJson(['success' => true, 'aantal' => 5]);

    $mandje->refresh();
    expect($mandje->materialen()->first()->pivot->aantal)->toBe(5);
});

// Test dat de validatie werkt bij een ongeldige hoeveelheid (minimum 1)
test('cart quantity update validates minimum', function () {
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
    ]);

    $mandje = Mandje::create(['gebruiker_id' => $technician->id]);
    $mandje->materialen()->attach($materiaal->id, ['aantal' => 2]);

    $response = $this->actingAs($technician)
        ->withHeaders(['Accept' => 'application/json'])
        ->patch(route('winkelmandje.update', $materiaal->id), [
            'aantal' => 0,
        ]);

    $response->assertStatus(422);
});
