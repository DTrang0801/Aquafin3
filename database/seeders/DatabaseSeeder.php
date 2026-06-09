<?php

namespace Database\Seeders;

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
            'province' => null,
            'email' => 'technieker@aquafin.test',
            'password' => bcrypt('password'),
            'role' => 'technieker',
        ]);

        User::factory()->create([
            'name' => 'Technieker Vlaams-Brabant',
            'province' => 'Vlaams-Brabant',
            'email' => 'technieker.vlaams-brabant@aquafin.test',
            'password' => bcrypt('password'),
            'role' => 'technieker',
        ]);

        User::factory()->create([
            'name' => 'Technieker West-Vlaanderen',
            'province' => 'West-Vlaanderen',
            'email' => 'technieker.west-vlaanderen@aquafin.test',
            'password' => bcrypt('password'),
            'role' => 'technieker',
        ]);

        User::factory()->create([
            'name' => 'Technieker Oost-Vlaanderen',
            'province' => 'Oost-Vlaanderen',
            'email' => 'technieker.oost-vlaanderen@aquafin.test',
            'password' => bcrypt('password'),
            'role' => 'technieker',
        ]);

        User::factory()->create([
            'name' => 'Technieker Limburg',
            'province' => 'Limburg',
            'email' => 'technieker.limburg@aquafin.test',
            'password' => bcrypt('password'),
            'role' => 'technieker',
        ]);

        User::factory()->create([
            'name' => 'Technieker Antwerpen',
            'province' => 'Antwerpen',
            'email' => 'technieker.antwerpen@aquafin.test',
            'password' => bcrypt('password'),
            'role' => 'technieker',
        ]);

        $this->call(NeerslagSeeder::class);
        $this->call(MateriaalcategorieSeeder::class);
        $this->call(MateriaalSubcategorieSeeder::class);
        $this->call(MateriaalSeeder::class);
        $this->call(BelangrijkeItemsSeeder::class);
        $this->call(BestellingenSeeder::class);
    }
}
