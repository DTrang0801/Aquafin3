<?php

namespace Database\Factories;

use App\Models\Materiaal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Materiaal>
 */
class MateriaalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'naam' => fake()->words(2, true),
            'beschrijving' => fake()->sentence(),
            'materiaal_subcategorie_id' => null,
            'belangrijk' => fake()->boolean(10),
        ];
    }
}
