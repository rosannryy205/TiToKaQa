<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Combo extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('combos')->insert([

            [
                'name' => 'Combo 1 Người',
                'price' => 89000,
                'image' => 'cb1.png',
                'description' => '1 Mỳ Cay Kim Chi Thập Cẩm + 1 Coca Size L',
                'quantity_sold' => 10,
            ],
            [
                'name' => 'Combo 2 Người',
                'price' => 169000,
                'image' => 'cb2.png',
                'description' => '1 Mỳ Cay Kim Chi Bò Mỹ + 1 Mỳ Cay Kim Chi Thập Cẩm + Kimbap Chiên',
                'quantity_sold' => 10,
            ],
            [
                'name' => 'Combo Panchan',
                'price' => 19000,
                'image' => 'cb3.png',
                'description' => '1 Kim Chi + 1 Cải Vàng + 1 Rong Biển Xốt',
                'quantity_sold' => 10,
            ],
        ]);
    }
}
