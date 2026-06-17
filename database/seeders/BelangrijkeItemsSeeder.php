<?php

namespace Database\Seeders;

use App\Enums\FloodRiskLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BelangrijkeItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Each material is assigned a minimum risk level:
     * - medium: flagged when rainfall reaches 100% of seasonal threshold
     * - high:   flagged only when rainfall reaches 120% of seasonal threshold
     */
    public function run(): void
    {
        $materials = [
            ['materiaal_id' => 1, 'risk_level' => FloodRiskLevel::Medium->value],
            ['materiaal_id' => 2, 'risk_level' => FloodRiskLevel::Medium->value],
            ['materiaal_id' => 3, 'risk_level' => FloodRiskLevel::High->value],
            ['materiaal_id' => 4, 'risk_level' => FloodRiskLevel::High->value],
            ['materiaal_id' => 5, 'risk_level' => FloodRiskLevel::Medium->value],
        ];

        DB::table('belangrijkeItems')->insert($materials);
    }
}
