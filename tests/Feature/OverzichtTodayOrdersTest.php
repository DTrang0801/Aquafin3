<?php

use App\Models\Bestelling;
use App\Models\Role;
use App\Models\User;

it('overzicht shows count of orders placed today', function () {
    $stockbeheerder = User::factory()->create(['role_id' => Role::STOCKBEHEERDER]);
    $technieker = User::factory()->create(['role_id' => Role::TECHNIEKER]);

    Bestelling::create([
        'gebruiker_id' => $technieker->id,
        'status' => 'actief',
        'gevraagde_datum' => now()->toDateString(),
        'locatie' => 'Test locatie',
    ]);

    Bestelling::create([
        'gebruiker_id' => $technieker->id,
        'status' => 'actief',
        'gevraagde_datum' => now()->toDateString(),
        'locatie' => 'Test locatie 2',
    ]);

    $response = $this->actingAs($stockbeheerder)->get(route('overzicht'));

    $response->assertSuccessful();
    $response->assertSee('2');
    $response->assertSee('bestellingen vandaag geplaatst');
});

it('overzicht shows no orders message when no orders today', function () {
    $stockbeheerder = User::factory()->create(['role_id' => Role::STOCKBEHEERDER]);

    $response = $this->actingAs($stockbeheerder)->get(route('overzicht'));

    $response->assertSuccessful();
    $response->assertSee('Geen nieuwe bestellingen vandaag');
});

it('overzicht shows singular form for one order', function () {
    $stockbeheerder = User::factory()->create(['role_id' => Role::STOCKBEHEERDER]);
    $technieker = User::factory()->create(['role_id' => Role::TECHNIEKER]);

    Bestelling::create([
        'gebruiker_id' => $technieker->id,
        'status' => 'actief',
        'gevraagde_datum' => now()->toDateString(),
        'locatie' => 'Test locatie',
    ]);

    $response = $this->actingAs($stockbeheerder)->get(route('overzicht'));

    $response->assertSuccessful();
    $response->assertSee('1');
    $response->assertSee('bestelling vandaag geplaatst');
});
