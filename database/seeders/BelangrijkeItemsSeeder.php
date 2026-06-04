<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BelangrijkeItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = [
            1,
            2,
            3,
            4,
            5,
        ];

        $rows = array_map(function ($materiaal_id){
            return [
                'materiaal_id' => $materiaal_id,
            ];
        }, $materials);

        DB::table('belangrijkeItems')->insert($rows);
    }
}
