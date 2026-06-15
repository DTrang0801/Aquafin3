<?php

use App\Models\User;

test('login page is accessible after logout', function () {
    $user = User::factory()->create();

    // User logs out
    $response = $this->actingAs($user)->post('/logout');

    // Verify they're logged out
    $this->assertGuest();
    $response->assertRedirect('/');

    // Verify login page is accessible
    $response = $this->get('/login');
    $response->assertStatus(200);
    $response->assertSee('Login');
});

test('root redirects to login when guest', function () {
    $response = $this->get('/');
    $response->assertRedirect('/login');
});

test('root redirects to materialen when authenticated', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');
    $response->assertRedirect('/materialen');
});

test('login form can be submitted after logout', function () {
    $user = User::factory()->create();

    // Logout
    $this->actingAs($user)->post('/logout');
    $this->assertGuest();

    // Login again
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
});
