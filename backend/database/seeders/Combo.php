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
                'name' => 'Combo cơ bản',
                'price' => 100000,
                'image' => 'mykimchithapcam.webp',
                'description' => '1 mì cay kim chi thập cẩm + 1 nước gạo Hàn Quốc'
            ],
        ]);
    }
}
