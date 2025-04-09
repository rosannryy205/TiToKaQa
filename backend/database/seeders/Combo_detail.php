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
                'quantity' => 1
            ],
            [
                'combo_id' => 1,
                'food_id' => 20,
                'quantity' => 1
            ],
            [
                'combo_id' => 2,
                'food_id' => 1,
                'quantity' => 1
            ],
            [
                'combo_id' => 2,
                'food_id' => 4,
                'quantity' => 1
            ],
            [
                'combo_id' => 2,
                'food_id' => 21,
                'quantity' => 1
            ],
            [
                'combo_id' => 3,
                'food_id' => 19,
                'quantity' => 1
            ],
            [
                'combo_id' => 3,
                'food_id' => 22,
                'quantity' => 1
            ],
            [
                'combo_id' => 3,
                'food_id' => 23,
                'quantity' => 1
            ],
        ]);
    }
}
