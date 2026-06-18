<?php

use App\Models\Role;
use App\Models\User;

// Test dat stockbeheerders na login direct naar materiaal beheer gaan
test('stockbeheerder wordt na login doorgestuurd naar materiaal beheer', function () {
    $stockbeheerder = User::factory()->create([
        'role_id' => Role::STOCKBEHEERDER,
    ]);

    $response = $this->post('/login', [
        'email' => $stockbeheerder->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('materialen.beheer'));
});

// Test dat technici na login naar materialen gaan
test('technieker wordt na login doorgestuurd naar materialen', function () {
    $technieker = User::factory()->create([
        'role_id' => Role::TECHNIEKER,
    ]);

    $response = $this->post('/login', [
        'email' => $technieker->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('materialen'));
});
