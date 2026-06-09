<?php

namespace Database\Seeders;

use App\Models\Bestelling;
use App\Models\Materiaal;
use App\Models\User;
use Illuminate\Database\Seeder;

class BestellingenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $technicians = User::where('role', 'technieker')->get();
        $materialen = Materiaal::all();

        if ($materialen->isEmpty()) {
            return;
        }

        foreach ($technicians as $technician) {
            // Create 10 orders, one roughly per month in the past 10 months
            for ($i = 0; $i < 10; $i++) {
                $monthsAgo = 9 - $i; // Goes from 9 months ago to recent
                $orderDate = now()->subMonths($monthsAgo)->subDays(rand(0, 28));

                $bestelling = Bestelling::create([
                    'gebruiker_id' => $technician->id,
                    'gevraagde_datum' => $orderDate,
                    'gevraagde_tijd' => null,
                    'locatie' => $technician->province ?? 'Onbekend',
                    'opmerking' => fake()->optional(0.7)->sentence(),
                    'custom_location_used' => false,
                ]);

                // Add 2-5 random materials to each order
                $randomMaterialen = $materialen->random(rand(2, min(5, $materialen->count())));

                foreach ($randomMaterialen as $materiaal) {
                    $bestelling->materialen()->attach($materiaal->id, [
                        'aantal' => rand(1, 10),
                    ]);
                }
            }
        }
    }
}


