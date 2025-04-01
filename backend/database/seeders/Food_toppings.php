<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Food_toppings extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('food_toppings')->insert([
            [
                'topping_id' => 12,
                'food_id' => 1,
                'price' => null,
                'description' => null

            ],
            [
                'topping_id' => 13,
                'food_id' => 1,
                'price' => null,
                'description' => null
            ],
            [
                'topping_id' => 14,
                'food_id' => 1,
                'price' => null,
                'description' => null

            ],
            [
                'topping_id' => 1,
                'food_id' => 1,
                'description' => '1 quả',
                'price' => 12000
            ],
            [
                'topping_id' => 2,
                'food_id' => 1,
                'description' => '100g',
                'price' => 15000
            ]
        ]);
    }
}
