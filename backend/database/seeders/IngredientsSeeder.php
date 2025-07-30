<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ingredients')->insert([
            // Các nguyên liệu chung từ mô tả món ăn
            ['id' => 1, 'name' => 'Mì Chinnoo', 'unit' => 'gói', 'quantity_in_stock' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Bò Mỹ', 'unit' => 'gram', 'quantity_in_stock' => 50000, 'created_at' => now(), 'updated_at' => now()], // 50kg
            ['id' => 3, 'name' => 'Tôm', 'unit' => 'gram', 'quantity_in_stock' => 10000, 'created_at' => now(), 'updated_at' => now()], // 10kg
            ['id' => 4, 'name' => 'Mực', 'unit' => 'gram', 'quantity_in_stock' => 10000, 'created_at' => now(), 'updated_at' => now()], // 10kg
            ['id' => 5, 'name' => 'Chả cá Hàn Quốc', 'unit' => 'gram', 'quantity_in_stock' => 5000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Cá viên', 'unit' => 'gram', 'quantity_in_stock' => 5000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'Kim chi', 'unit' => 'gram', 'quantity_in_stock' => 20000, 'created_at' => now(), 'updated_at' => now()], // 20kg
            ['id' => 8, 'name' => 'Cải thìa', 'unit' => 'gram', 'quantity_in_stock' => 3000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'name' => 'Nấm', 'unit' => 'gram', 'quantity_in_stock' => 3000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'name' => 'Bắp cải tím', 'unit' => 'gram', 'quantity_in_stock' => 3000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'name' => 'Đùi gà', 'unit' => 'cái', 'quantity_in_stock' => 100, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'name' => 'Xúc xích', 'unit' => 'cây', 'quantity_in_stock' => 200, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'name' => 'Cá', 'unit' => 'gram', 'quantity_in_stock' => 5000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'name' => 'Heo', 'unit' => 'gram', 'quantity_in_stock' => 50000, 'created_at' => now(), 'updated_at' => now()], // 50kg
            ['id' => 15, 'name' => 'Hành tây', 'unit' => 'củ', 'quantity_in_stock' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'name' => 'Ớt chuông', 'unit' => 'quả', 'quantity_in_stock' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'name' => 'Cà rốt', 'unit' => 'củ', 'quantity_in_stock' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'name' => 'Hành baro', 'unit' => 'cây', 'quantity_in_stock' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'name' => 'Tokbokki', 'unit' => 'gram', 'quantity_in_stock' => 10000, 'created_at' => now(), 'updated_at' => now()], // 10kg
            ['id' => 20, 'name' => 'Cải thảo', 'unit' => 'gram', 'quantity_in_stock' => 3000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'name' => 'Bắp', 'unit' => 'bắp', 'quantity_in_stock' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'name' => 'Phô mai viên', 'unit' => 'viên', 'quantity_in_stock' => 200, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'name' => 'Nước gạo', 'unit' => 'chai', 'quantity_in_stock' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'name' => 'Coca', 'unit' => 'chai', 'quantity_in_stock' => 100, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'name' => 'Kimbap', 'unit' => 'cuộn', 'quantity_in_stock' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'name' => 'Cải Vàng', 'unit' => 'gram', 'quantity_in_stock' => 1000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 27, 'name' => 'Rong Biển', 'unit' => 'gram', 'quantity_in_stock' => 1000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 28, 'name' => 'Nước lẩu kim chi', 'unit' => 'lít', 'quantity_in_stock' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 29, 'name' => 'Thịt bò Mỹ', 'unit' => 'gram', 'quantity_in_stock' => 50000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 30, 'name' => 'Bò viên', 'unit' => 'gram', 'quantity_in_stock' => 5000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 31, 'name' => 'Chả cá sợi', 'unit' => 'gram', 'quantity_in_stock' => 2000, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
