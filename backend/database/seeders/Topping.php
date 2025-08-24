<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Topping extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('toppings')->insert([
            [
                'name' => 'Trứng Ngâm Tương', // id 1
                'price' => 12000,
                'category_id' => 16
            ],
            [
                'name' => 'Bắp Cải Tím', // id 2
                'price' => 15000,
                'category_id' => 16
            ],
            [
                'name' => 'Nấm Kim Châm Thêm', // id 3
                'price' => 15000,
                'category_id' => 16
            ],
            [
                'name' => 'Mực Cắt Khoanh Thêm', // id 4
                'price' => 15000,
                'category_id' => 16
            ],
            [
                'name' => 'Tôm Thêm', // id 5
                'price' => 15000,
                'category_id' => 16
            ],
            [
                'name' => 'Thịt Heo Cuộn ', // id 6
                'price' => 15000,
                'category_id' => 16
            ],
            [
                'name' => 'Cá Viên Thêm', // id 7
                'price' => 15000,
                'category_id' => 16
            ],
            [
                'name' => 'Xúc Xích', // id 8
                'price' => 15000,
                'category_id' => 16
            ],
            [
                'name' => 'Bò Thêm', // id 9
                'price' => 19000,
                'category_id' => 16
            ],
            [
                'name' => 'Mì Nấu Thêm', // id 10
                'price' => 15000,
                'category_id' => 16
            ],
            [
                'name' => 'Trứng Gà', // id 11
                'price' => 9000,
                'category_id' => 16
            ],
            [
                'name' => 'Cấp 0', // id 12
                'price' => null,
                'category_id' => 15
            ],
            [
                'name' => 'Cấp 1', // id 12
                'price' => null,
                'category_id' => 15
            ],
            [
                'name' => 'Cấp 2', // id 13
                'price' => null,
                'category_id' => 15
            ],
            [
                'name' => 'Cấp 3', // id 14
                'price' => null,
                'category_id' => 15
            ],
            [
                'name' => 'Cấp 4', // id 15
                'price' => null,
                'category_id' => 15
            ],
            [
                'name' => 'Cấp 5', // id 16
                'price' => null,
                'category_id' => 15
            ],
            [
                'name' => 'Cấp 6', // id 17
                'price' => null,
                'category_id' => 15
            ],
            [
                'name' => 'Cấp 7', // id 18
                'price' => null,
                'category_id' => 15
            ]
        ]);
    }
}
