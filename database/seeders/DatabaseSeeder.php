<?php

namespace Database\Seeders;

use App\Models\Bestelling;
use App\Models\Materiaal;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@aquafin.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Stockbeheerder',
            'email' => 'stock@aquafin.test',
            'password' => bcrypt('password'),
            'role' => 'stockbeheerder',
        ]);

        User::factory()->create([
            'name' => 'Technieker',
            'email' => 'technieker@aquafin.test',
            'password' => bcrypt('password'),
            'role' => 'technieker',
        ]);

        $this->call(NeerslagSeeder::class);
        $this->call(MateriaalcategorieSeeder::class);
        $this->call(MateriaalSubcategorieSeeder::class);
        $this->call(MateriaalSeeder::class);

        $technieker = User::where('email', 'technieker@aquafin.test')->first();
        $materialen = Materiaal::take(3)->get();

        $bestelling = Bestelling::create([
            'gebruiker_id' => $technieker->id,
            'gevraagde_datum' => '2026-06-10',
            'gevraagde_tijd' => '09:00',
            'locatie' => 'Brussel - Site A',
            'opmerking' => 'Dringend nodig voor onderhoud.',
        ]);

        $bestelling->materialen()->attach([
            $materialen[0]->id => ['aantal' => 2],
            $materialen[1]->id => ['aantal' => 1],
        ]);

        $bestelling2 = Bestelling::create([
            'gebruiker_id' => $technieker->id,
            'gevraagde_datum' => '2026-06-15',
            'gevraagde_tijd' => '14:00',
            'locatie' => 'Gent - Site B',
            'opmerking' => null,
        ]);

        $bestelling2->materialen()->attach([
            $materialen[2]->id => ['aantal' => 3],
        ]);

    }
}
