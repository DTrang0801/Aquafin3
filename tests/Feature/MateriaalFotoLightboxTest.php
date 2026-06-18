<?php

use App\Models\Materiaal;
use App\Models\Materiaalcategorie;
use App\Models\MateriaalSubcategorie;
use App\Models\Role;
use App\Models\User;

// Test dat de lightbox HTML aanwezig is op de materialen pagina
test('lightbox modal is aanwezig op materialen pagina', function () {
    $technieker = User::factory()->create([
        'role_id' => Role::TECHNIEKER,
    ]);

    $response = $this->actingAs($technieker)->get(route('materialen'));

    $response->assertOk();
    $response->assertSee('lightbox-overlay', false);
    $response->assertSee('openLightbox', false);
});

// Test dat foto's een onclick handler hebben voor de lightbox
test('foto\'s hebben lightbox onclick handler', function () {
    $categorie = Materiaalcategorie::create(['naam' => 'Test Categorie']);
    $subcategorie = MateriaalSubcategorie::create([
        'naam' => 'Test Subcategorie',
        'materiaal_categorie_id' => $categorie->id,
    ]);

    $materiaal = Materiaal::create([
        'naam' => 'Test Materiaal met Foto',
        'materiaal_subcategorie_id' => $subcategorie->id,
        'eenheid' => 'stuk',
        'foto' => 'materialen/test.jpg',
    ]);

    $technieker = User::factory()->create([
        'role_id' => Role::TECHNIEKER,
    ]);

    $response = $this->actingAs($technieker)->get(route('materialen'));

    $response->assertOk();
    $response->assertSee('openLightbox', false);
});
