<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaalcategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Bevestigingsmateriaal',
            'Persoonlijke beschermingsmiddelen (PBM)',
            'Gereedschap (manueel & elektrisch)',
            'Technische onderhoudsmaterialen',
            'Specifieke Aquafin/riolering gerelateerde tools',
            'Diversen / Verbruiksgoederen',
        ];

        $now = now();
        $rows = array_map(function ($name) use ($now) {
            return [
                'naam' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $categories);

        DB::table('materiaal_categorieen')->insert($rows);
    }
}
