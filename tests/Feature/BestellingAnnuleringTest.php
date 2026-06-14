<?php

use App\Models\Bestelling;
use App\Models\Role;
use App\Models\User;

it('stockbeheerder kan bestelling annuleren', function () {
    $stockbeheerder = User::factory()->create(['role_id' => Role::STOCKBEHEERDER]);
    $technieker = User::factory()->create(['role_id' => Role::TECHNIEKER]);

    $bestelling = Bestelling::create([
        'gebruiker_id' => $technieker->id,
        'status' => 'actief',
        'gevraagde_datum' => now()->toDateString(),
        'locatie' => 'Test locatie',
    ]);

    $this->actingAs($stockbeheerder)
        ->post(route('bestellingen.annuleer', $bestelling->id))
        ->assertRedirect();

    $bestelling->refresh();
    expect($bestelling->status)->toBe('geannuleerd');
});

it('technieker kan bestelling niet annuleren', function () {
    $technieker = User::factory()->create(['role_id' => Role::TECHNIEKER]);

    $bestelling = Bestelling::create([
        'gebruiker_id' => $technieker->id,
        'status' => 'actief',
        'gevraagde_datum' => now()->toDateString(),
        'locatie' => 'Test locatie',
    ]);

    $this->actingAs($technieker)
        ->post(route('bestellingen.annuleer', $bestelling->id))
        ->assertForbidden();

    $bestelling->refresh();
    expect($bestelling->status)->toBe('actief');
});

it('admin kan bestelling annuleren', function () {
    $admin = User::factory()->create(['role_id' => Role::ADMIN]);
    $technieker = User::factory()->create(['role_id' => Role::TECHNIEKER]);

    $bestelling = Bestelling::create([
        'gebruiker_id' => $technieker->id,
        'status' => 'actief',
        'gevraagde_datum' => now()->toDateString(),
        'locatie' => 'Test locatie',
    ]);

    $this->actingAs($admin)
        ->post(route('bestellingen.annuleer', $bestelling->id))
        ->assertRedirect();

    $bestelling->refresh();
    expect($bestelling->status)->toBe('geannuleerd');
});

it('kan reeds geannuleerde bestelling niet opnieuw annuleren', function () {
    $stockbeheerder = User::factory()->create(['role_id' => Role::STOCKBEHEERDER]);
    $technieker = User::factory()->create(['role_id' => Role::TECHNIEKER]);

    $bestelling = Bestelling::create([
        'gebruiker_id' => $technieker->id,
        'status' => 'geannuleerd',
        'gevraagde_datum' => now()->toDateString(),
        'locatie' => 'Test locatie',
    ]);

    $this->actingAs($stockbeheerder)
        ->post(route('bestellingen.annuleer', $bestelling->id))
        ->assertForbidden();
});

it('geannuleerde bestelling heeft correcte status', function () {
    $bestelling = Bestelling::create([
        'gebruiker_id' => User::factory()->create()->id,
        'status' => 'actief',
        'gevraagde_datum' => now()->toDateString(),
        'locatie' => 'Test locatie',
    ]);

    expect($bestelling->isGeannuleerd())->toBeFalse();

    $bestelling->annuleer();

    expect($bestelling->isGeannuleerd())->toBeTrue();
    expect($bestelling->status)->toBe('geannuleerd');
});

it('overzicht toont geannuleerde bestelling met badge', function () {
    $stockbeheerder = User::factory()->create(['role_id' => Role::STOCKBEHEERDER]);
    $technieker = User::factory()->create(['role_id' => Role::TECHNIEKER]);

    $bestelling = Bestelling::create([
        'gebruiker_id' => $technieker->id,
        'status' => 'geannuleerd',
        'gevraagde_datum' => now()->toDateString(),
        'locatie' => 'Test locatie',
    ]);

    $this->actingAs($stockbeheerder)
        ->get(route('overzicht'))
        ->assertSee('Geannuleerd')
        ->assertDontSee('Annuleer bestelling');
});

it('overzicht toont actieve bestelling met annuleer-knop', function () {
    $stockbeheerder = User::factory()->create(['role_id' => Role::STOCKBEHEERDER]);
    $technieker = User::factory()->create(['role_id' => Role::TECHNIEKER]);

    $bestelling = Bestelling::create([
        'gebruiker_id' => $technieker->id,
        'status' => 'actief',
        'gevraagde_datum' => now()->toDateString(),
        'locatie' => 'Test locatie',
    ]);

    $this->actingAs($stockbeheerder)
        ->get(route('overzicht'))
        ->assertSee('Annuleer bestelling');
});
