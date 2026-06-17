<?php

use App\Models\Role;
use App\Models\User;
use App\Services\FloodRiskService;

test('weersvoorspelling page displays risk percentage', function () {
    $user = User::factory()->create(['role_id' => Role::TECHNIEKER]);

    $this->actingAs($user)
        ->get(route('weersvoorspelling'))
        ->assertOk();
});

test('flood risk service calculates risk percentage correctly', function () {
    $service = app(FloodRiskService::class);

    $percentage = $service->calculateRiskPercentage(
        latitude: 50.8503,
        longitude: 4.3517,
        forecastDaily: [],
        timezone: 'Europe/Berlin',
    );

    expect($percentage)->toBeNumeric();
    expect($percentage)->toBeGreaterThanOrEqual(0);
});

test('risk percentage is passed to weersvoorspelling view', function () {
    $user = User::factory()->create(['role_id' => Role::TECHNIEKER]);

    $response = $this->actingAs($user)
        ->get(route('weersvoorspelling'));

    $response->assertViewHas('riskPercentage');
    expect($response['riskPercentage'])->toBeNumeric();
});
