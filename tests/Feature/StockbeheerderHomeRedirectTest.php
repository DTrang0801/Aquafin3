<?php

use App\Models\Role;
use App\Models\User;

// Test dat stockbeheerders worden doorgestuurd naar materiaal beheer
test('stockbeheerder wordt doorgestuurd naar materiaal beheer', function () {
    $stockbeheerder = User::factory()->create([
        'role_id' => Role::STOCKBEHEERDER,
    ]);

    $response = $this->actingAs($stockbeheerder)
        ->get(route('home.page'));

    $response->assertRedirect(route('materialen.beheer'));
});

// Test dat technici de home pagina zien
test('technieker ziet de home pagina', function () {
    $technieker = User::factory()->create([
        'role_id' => Role::TECHNIEKER,
    ]);

    $response = $this->actingAs($technieker)
        ->get(route('home.page'));

    $response->assertOk();
    $response->assertSee('Materiaal bestellen');
});
