<?php

use App\Models\Bestelling;
use App\Models\Materiaal;
use App\Models\Role;
use App\Models\User;

test('technician can add new materials to order', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'can_edit_until' => now()->addDay(),
    ]);

    // Create materials and attach one
    $material1 = Materiaal::factory()->create(['naam' => 'Material 1']);
    $material2 = Materiaal::factory()->create(['naam' => 'Material 2']);
    $bestelling->materialen()->attach($material1->id, ['aantal' => 5]);

    // Update order, adding a new material
    $response = $this->actingAs($techniker)
        ->put(route('bestellingen.update', $bestelling->id), [
            'gevraagde_datum' => now()->addDays(5)->format('Y-m-d'),
            'locatie' => 'Location',
            'use_custom_location' => false,
            'materials' => [
                $material1->id => 10,  // Update existing
                $material2->id => 3,   // Add new
            ],
        ]);

    $response->assertRedirect(route('bestellingen'));

    $bestelling->refresh();

    // Check that both materials are attached with correct quantities
    expect($bestelling->materialen()->count())->toBe(2);
    expect($bestelling->materialen()->find($material1->id)->pivot->aantal)->toBe(10);
    expect($bestelling->materialen()->find($material2->id)->pivot->aantal)->toBe(3);
});

test('technician can remove materials from order', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'can_edit_until' => now()->addDay(),
    ]);

    $material1 = Materiaal::factory()->create(['naam' => 'Material 1']);
    $material2 = Materiaal::factory()->create(['naam' => 'Material 2']);

    $bestelling->materialen()->attach($material1->id, ['aantal' => 5]);
    $bestelling->materialen()->attach($material2->id, ['aantal' => 3]);

    // Remove material1, keep material2
    $response = $this->actingAs($techniker)
        ->put(route('bestellingen.update', $bestelling->id), [
            'gevraagde_datum' => now()->addDays(5)->format('Y-m-d'),
            'locatie' => 'Location',
            'use_custom_location' => false,
            'materials' => [
                $material2->id => 5,
            ],
            'removed_materials' => [
                $material1->id => 1,
            ],
        ]);

    $response->assertRedirect(route('bestellingen'));

    $bestelling->refresh();

    // Check that only material2 is attached
    expect($bestelling->materialen()->count())->toBe(1);
    expect($bestelling->materialen()->find($material2->id)->pivot->aantal)->toBe(5);
    expect($bestelling->materialen()->find($material1->id))->toBeNull();
});

test('technician can change material quantities', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'can_edit_until' => now()->addDay(),
    ]);

    $material = Materiaal::factory()->create(['naam' => 'Material 1']);
    $bestelling->materialen()->attach($material->id, ['aantal' => 5]);

    // Update quantity
    $response = $this->actingAs($techniker)
        ->put(route('bestellingen.update', $bestelling->id), [
            'gevraagde_datum' => now()->addDays(5)->format('Y-m-d'),
            'locatie' => 'Location',
            'use_custom_location' => false,
            'materials' => [
                $material->id => 15,
            ],
        ]);

    $response->assertRedirect(route('bestellingen'));

    $bestelling->refresh();
    expect($bestelling->materialen()->find($material->id)->pivot->aantal)->toBe(15);
});

test('order is marked as edited after material changes', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'can_edit_until' => now()->addDay(),
        'is_edited' => false,
    ]);

    $material1 = Materiaal::factory()->create();
    $material2 = Materiaal::factory()->create();
    $bestelling->materialen()->attach($material1->id, ['aantal' => 5]);

    $this->actingAs($techniker)
        ->put(route('bestellingen.update', $bestelling->id), [
            'gevraagde_datum' => now()->addDays(5)->format('Y-m-d'),
            'locatie' => 'Location',
            'use_custom_location' => false,
            'materials' => [
                $material1->id => 10,
                $material2->id => 3,
            ],
        ]);

    $bestelling->refresh();
    expect($bestelling->is_edited)->toBeTrue();
});

test('cannot add invalid material quantity', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'can_edit_until' => now()->addDay(),
    ]);

    $material = Materiaal::factory()->create();

    $response = $this->actingAs($techniker)
        ->put(route('bestellingen.update', $bestelling->id), [
            'gevraagde_datum' => now()->addDays(5)->format('Y-m-d'),
            'locatie' => 'Location',
            'use_custom_location' => false,
            'materials' => [
                $material->id => 0,  // Invalid: must be >= 1
            ],
        ]);

    $response->assertSessionHasErrors('materials.*');
});

test('cannot add material with quantity exceeding limit', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'can_edit_until' => now()->addDay(),
    ]);

    $material = Materiaal::factory()->create();

    $response = $this->actingAs($techniker)
        ->put(route('bestellingen.update', $bestelling->id), [
            'gevraagde_datum' => now()->addDays(5)->format('Y-m-d'),
            'locatie' => 'Location',
            'use_custom_location' => false,
            'materials' => [
                $material->id => 10001,  // Invalid: must be <= 10000
            ],
        ]);

    $response->assertSessionHasErrors('materials.*');
});
