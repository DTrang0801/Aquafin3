<?php

use App\Models\Bestelling;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('technieker can edit their own order within the edit window', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'can_edit_until' => now()->addHours(12),
        'is_edited' => false,
    ]);

    $this->assertTrue(Gate::forUser($techniker)->allows('update', $bestelling));
    $this->assertTrue($bestelling->canStillBeEdited());
});

test('technieker cannot edit order after the edit window has expired', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'can_edit_until' => now()->subHours(1),
        'is_edited' => false,
    ]);

    $this->assertFalse($bestelling->canStillBeEdited());
    $this->assertFalse(Gate::forUser($techniker)->allows('update', $bestelling));
});

test('technieker cannot edit other users orders', function () {
    $techniker1 = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $techniker2 = User::factory()->create(['role_id' => Role::TECHNIEKER]);

    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker1->id,
        'can_edit_until' => now()->addDay(),
    ]);

    $this->assertFalse(Gate::forUser($techniker2)->allows('update', $bestelling));
});

test('order is marked as edited when updated', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
        'is_edited' => false,
    ]);

    $this->assertFalse($bestelling->is_edited);

    $bestelling->markAsEdited();

    $this->assertTrue($bestelling->fresh()->is_edited);
});

test('order can_edit_until is set to one day after creation', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);

    $beforeCreation = now();
    $bestelling = Bestelling::factory()->create([
        'gebruiker_id' => $techniker->id,
    ]);
    $afterCreation = now();

    $this->assertNotNull($bestelling->can_edit_until);

    $expectedMinimum = $beforeCreation->clone()->addDay()->subSecond();
    $expectedMaximum = $afterCreation->clone()->addDay()->addSecond();

    $this->assertTrue(
        $bestelling->can_edit_until->isBetween($expectedMinimum, $expectedMaximum),
        "can_edit_until should be approximately one day from now, but got {$bestelling->can_edit_until}"
    );
});

test('non-technieker cannot create orders', function () {
    $user = User::factory()->create(['role_id' => Role::STOCKBEHEERDER]);

    $this->assertFalse(Gate::forUser($user)->allows('create', Bestelling::class));
});

test('technieker can create orders', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);

    $this->assertTrue(Gate::forUser($techniker)->allows('create', Bestelling::class));
});

test('technieker can view any orders', function () {
    $techniker = User::factory()->create(['role_id' => Role::TECHNIEKER]);
    $bestelling = Bestelling::factory()->create();

    $this->assertTrue(Gate::forUser($techniker)->allows('viewAny', Bestelling::class));
    $this->assertTrue(Gate::forUser($techniker)->allows('view', $bestelling));
});

test('non-technieker cannot view orders', function () {
    $user = User::factory()->create(['role_id' => Role::STOCKBEHEERDER]);

    $this->assertFalse(Gate::forUser($user)->allows('viewAny', Bestelling::class));
});
