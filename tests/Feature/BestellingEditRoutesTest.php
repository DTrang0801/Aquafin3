<?php

use App\Models\Bestelling;
use App\Models\Role;
use App\Models\User;

test('technieker can view edit form for their own order within edit window', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'can_edit_until' => now()->addHours(12),
    ]);

    $response = $this->actingAs($techniker)
        ->get(route('bestellingen.edit', $bestelling->id));

    $response->assertSuccessful();
    $response->assertViewIs('pages.bewerk-bestelling');
    $response->assertViewHas('bestelling', $bestelling);
});

test('technieker cannot view edit form after edit window expires', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'can_edit_until' => now()->subHours(1),
    ]);

    $response = $this->actingAs($techniker)
        ->get(route('bestellingen.edit', $bestelling->id));

    $response->assertForbidden();
});

test('technieker cannot view edit form for other users orders', function () {
    $techniker1 = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $techniker2 = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker1->id,
        'can_edit_until' => now()->addDay(),
    ]);

    $response = $this->actingAs($techniker2)
        ->get(route('bestellingen.edit', $bestelling->id));

    $response->assertForbidden();
});

test('technieker can update their own order within edit window', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'can_edit_until' => now()->addHours(12),
        'is_edited' => false,
        'opmerking' => 'Old remark',
    ]);

    $response = $this->actingAs($techniker)
        ->put(route('bestellingen.update', $bestelling->id), [
            'gevraagde_datum' => now()->addDays(5)->format('Y-m-d'),
            'locatie' => 'New Location',
            'opmerking' => 'Updated remark',
            'use_custom_location' => false,
        ]);

    $response->assertRedirect(route('bestellingen'));

    $bestelling->refresh();
    expect($bestelling->locatie)->toBe('New Location');
    expect($bestelling->opmerking)->toBe('Updated remark');
    expect($bestelling->is_edited)->toBeTrue();
});

test('updated order is marked as edited', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'can_edit_until' => now()->addDay(),
        'is_edited' => false,
    ]);

    $this->actingAs($techniker)
        ->put(route('bestellingen.update', $bestelling->id), [
            'gevraagde_datum' => now()->addDays(5)->format('Y-m-d'),
            'locatie' => 'Updated Location',
            'use_custom_location' => false,
        ]);

    $bestelling->refresh();
    expect($bestelling->is_edited)->toBeTrue();
});

test('technieker cannot update order after edit window expires', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'can_edit_until' => now()->subHours(1),
    ]);

    $response = $this->actingAs($techniker)
        ->put(route('bestellingen.update', $bestelling->id), [
            'gevraagde_datum' => now()->addDays(5)->format('Y-m-d'),
            'locatie' => 'New Location',
            'use_custom_location' => false,
        ]);

    $response->assertForbidden();
});

test('order update requires valid date', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'can_edit_until' => now()->addDay(),
    ]);

    $response = $this->actingAs($techniker)
        ->put(route('bestellingen.update', $bestelling->id), [
            'gevraagde_datum' => now()->subDays(1)->format('Y-m-d'),
            'locatie' => 'New Location',
            'use_custom_location' => false,
        ]);

    $response->assertSessionHasErrors('gevraagde_datum');
});

test('order update validates location length', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'can_edit_until' => now()->addDay(),
    ]);

    $longLocation = str_repeat('a', 500);

    $response = $this->actingAs($techniker)
        ->put(route('bestellingen.update', $bestelling->id), [
            'gevraagde_datum' => now()->addDays(5)->format('Y-m-d'),
            'locatie' => $longLocation,
            'use_custom_location' => false,
        ]);

    $response->assertSessionHasErrors('locatie');
});

test('stockbeheerder can update any order', function () {
    $stockbeheerder = User::factory()->create(['role_id' => Role::STOCKBEHEERDER]);
    $bestelling = Bestelling::factory()->create([
        'can_edit_until' => now()->addDay(),
        'is_edited' => false,
    ]);

    $response = $this->actingAs($stockbeheerder)
        ->put(route('bestellingen.update', $bestelling->id), [
            'gevraagde_datum' => now()->addDays(5)->format('Y-m-d'),
            'locatie' => 'New Location',
            'use_custom_location' => false,
        ]);

    $response->assertRedirect(route('overzicht'));

    $bestelling->refresh();
    expect($bestelling->locatie)->toBe('New Location');
    expect($bestelling->is_edited)->toBeTrue();
});

test('admin can update any order', function () {
    $admin = User::factory()->create(['role_id' => Role::ADMIN]);
    $bestelling = Bestelling::factory()->create([
        'can_edit_until' => now()->addDay(),
        'is_edited' => false,
    ]);

    $response = $this->actingAs($admin)
        ->put(route('bestellingen.update', $bestelling->id), [
            'gevraagde_datum' => now()->addDays(5)->format('Y-m-d'),
            'locatie' => 'New Location',
            'use_custom_location' => false,
        ]);

    $response->assertRedirect(route('overzicht'));

    $bestelling->refresh();
    expect($bestelling->locatie)->toBe('New Location');
    expect($bestelling->is_edited)->toBeTrue();
});

test('user cannot update cancelled order', function () {
    $stockbeheerder = User::factory()->create(['role_id' => Role::STOCKBEHEERDER]);
    $bestelling = Bestelling::factory()->create([
        'can_edit_until' => now()->addDay(),
        'status' => Bestelling::STATUS_GEANNULEERD,
    ]);

    $response = $this->actingAs($stockbeheerder)
        ->put(route('bestellingen.update', $bestelling->id), [
            'gevraagde_datum' => now()->addDays(5)->format('Y-m-d'),
            'locatie' => 'New Location',
            'use_custom_location' => false,
        ]);

    $response->assertForbidden();
});
