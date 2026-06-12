<?php

namespace Database\Factories;

use App\Models\Bestelling;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bestelling>
 */
class BestellingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gebruiker_id' => User::factory(),
            'gevraagde_datum' => fake()->date(),
            'gevraagde_tijd' => fake()->time(),
            'locatie' => fake()->sentence(),
            'opmerking' => fake()->sentence(),
            'custom_location_used' => false,
            'is_edited' => false,
            'can_edit_until' => now()->addDay(),
        ];
    }
}
