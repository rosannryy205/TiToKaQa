<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Combo_detail extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('combo_details')->insert([
            [
                'combo_id' => 1,
                'food_id' => 1,
                'quantity' => 10
            ],
            [
                'combo_id' => 1,
                'food_id' => 18,
                'quantity' => 10
            ],
        ]);
    }
}
