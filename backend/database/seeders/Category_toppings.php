<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Category_toppings extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('category_toppings')->insert([
            [
                'name' => 'Cấp độ'
            ],
            [
                'name' => 'Topping'
            ]
        ]);
    }
}
