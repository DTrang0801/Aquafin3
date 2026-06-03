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
        ]);

        User::factory()->create([
            'name' => 'Stockbeheerder',
            'email' => 'stock@aquafin.test',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Technieker',
            'email' => 'technieker@aquafin.test',
            'password' => bcrypt('password'),
        ]);

        $this->call(NeerslagSeeder::class);
        $this->call(MateriaalcategorieSeeder::class);
        $this->call(MateriaalSubcategorieSeeder::class);
        $this->call(MateriaalSeeder::class);
    }
}
