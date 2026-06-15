<?php

use App\Models\Bestelling;
use App\Models\Materiaal;
use App\Models\Materiaalcategorie;
use App\Models\MateriaalSubcategorie;
use App\Models\Role;
use App\Models\User;

it('soft deletes materiaal and excludes it from queries', function () {
    $materiaal = Materiaal::create([
        'naam' => 'Test Material',
        'beschrijving' => 'Test Description',
    ]);

    expect(Materiaal::count())->toBe(1);

    $materiaal->delete();

    expect(Materiaal::count())->toBe(0);
    expect(Materiaal::withTrashed()->count())->toBe(1);
    expect($materiaal->trashed())->toBeTrue();
});

it('soft deletes materiaal subcategorie', function () {
    $categorie = Materiaalcategorie::create([
        'naam' => 'Test Category',
    ]);

    $subcategorie = MateriaalSubcategorie::create([
        'naam' => 'Test Subcategory',
        'materiaal_categorie_id' => $categorie->id,
    ]);

    expect(MateriaalSubcategorie::count())->toBe(1);

    $subcategorie->delete();

    expect(MateriaalSubcategorie::count())->toBe(0);
    expect(MateriaalSubcategorie::withTrashed()->count())->toBe(1);
});

it('soft deletes materiaal categorie', function () {
    $categorie = Materiaalcategorie::create([
        'naam' => 'Test Category',
    ]);

    expect(Materiaalcategorie::count())->toBe(1);

    $categorie->delete();

    expect(Materiaalcategorie::count())->toBe(0);
    expect(Materiaalcategorie::withTrashed()->count())->toBe(1);
});

it('soft deletes bestelling', function () {
    $user = User::factory()->create();
    $bestelling = Bestelling::create([
        'gebruiker_id' => $user->id,
        'gevraagde_datum' => now()->toDateString(),
        'gevraagde_tijd' => '10:00',
        'locatie' => 'Test Location',
    ]);

    expect(Bestelling::count())->toBe(1);

    $bestelling->delete();

    expect(Bestelling::count())->toBe(0);
    expect(Bestelling::withTrashed()->count())->toBe(1);
});

it('soft deletes user', function () {
    $user = User::factory()->create();

    expect(User::count())->toBeGreaterThanOrEqual(1);

    $userCount = User::count();
    $user->delete();

    expect(User::count())->toBe($userCount - 1);
    expect(User::withTrashed()->count())->toBe($userCount);
});

it('can restore soft deleted records', function () {
    $materiaal = Materiaal::create([
        'naam' => 'Test Material',
        'beschrijving' => 'Test Description',
    ]);

    $materiaal->delete();
    expect(Materiaal::count())->toBe(0);

    $materiaal->restore();
    expect(Materiaal::count())->toBe(1);
    expect($materiaal->trashed())->toBeFalse();
});

it('can force delete soft deleted records', function () {
    $materiaal = Materiaal::create([
        'naam' => 'Test Material',
        'beschrijving' => 'Test Description',
    ]);

    $materiaal->delete();
    expect(Materiaal::withTrashed()->count())->toBe(1);

    $materiaal->forceDelete();
    expect(Materiaal::withTrashed()->count())->toBe(0);
});

it('soft deleted materials do not appear in queries', function () {
    $activeMaterial = Materiaal::create([
        'naam' => 'Active Material',
    ]);

    $deletedMaterial = Materiaal::create([
        'naam' => 'Deleted Material',
    ]);

    $deletedMaterial->delete();

    expect(Materiaal::count())->toBe(1);
    expect(Materiaal::where('naam', 'Active Material')->count())->toBe(1);
    expect(Materiaal::where('naam', 'Deleted Material')->count())->toBe(0);
});

it('only stockbeheerder can soft delete materials', function () {
    $user = User::factory()->create(['role_id' => null]);
    $stockbeheerder = User::factory()->create(['role_id' => Role::STOCKBEHEERDER]);
    $materiaal = Materiaal::create([
        'naam' => 'Test Material',
    ]);

    $response = $this->actingAs($user)->delete("/materialen/{$materiaal->id}");
    $response->assertStatus(403);

    expect(Materiaal::count())->toBe(1);

    $response = $this->actingAs($stockbeheerder)->delete("/materialen/{$materiaal->id}");
    $response->assertRedirect(route('materialen.beheer'));

    expect(Materiaal::count())->toBe(0);
    expect(Materiaal::withTrashed()->count())->toBe(1);
});
