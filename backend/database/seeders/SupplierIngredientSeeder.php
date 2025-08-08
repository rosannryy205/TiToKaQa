<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierIngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('supplier_ingredient')->insert([
            // Cửa hàng thực phẩm tươi sống An Phát (supplier_id = 1)
            ['supplier_id' => 1, 'ingredient_id' => 2, 'price' => 250000, 'supply_date' => '2025-07-20', 'created_at' => now(), 'updated_at' => now()], // Bò Mỹ
            ['supplier_id' => 1, 'ingredient_id' => 3, 'price' => 180000, 'supply_date' => '2025-07-20', 'created_at' => now(), 'updated_at' => now()], // Tôm
            ['supplier_id' => 1, 'ingredient_id' => 4, 'price' => 150000, 'supply_date' => '2025-07-20', 'created_at' => now(), 'updated_at' => now()], // Mực
            ['supplier_id' => 1, 'ingredient_id' => 11, 'price' => 45000, 'supply_date' => '2025-07-20', 'created_at' => now(), 'updated_at' => now()], // Đùi gà
            ['supplier_id' => 1, 'ingredient_id' => 7, 'price' => 30000, 'supply_date' => '2025-07-20', 'created_at' => now(), 'updated_at' => now()], // Kim chi
            ['supplier_id' => 1, 'ingredient_id' => 8, 'price' => 15000, 'supply_date' => '2025-07-20', 'created_at' => now(), 'updated_at' => now()], // Cải thìa
            ['supplier_id' => 1, 'ingredient_id' => 9, 'price' => 25000, 'supply_date' => '2025-07-20', 'created_at' => now(), 'updated_at' => now()], // Nấm
            ['supplier_id' => 1, 'ingredient_id' => 10, 'price' => 18000, 'supply_date' => '2025-07-20', 'created_at' => now(), 'updated_at' => now()], // Bắp cải tím
            ['supplier_id' => 1, 'ingredient_id' => 14, 'price' => 120000, 'supply_date' => '2025-07-20', 'created_at' => now(), 'updated_at' => now()], // Heo
            ['supplier_id' => 1, 'ingredient_id' => 15, 'price' => 10000, 'supply_date' => '2025-07-20', 'created_at' => now(), 'updated_at' => now()], // Hành tây
            ['supplier_id' => 1, 'ingredient_id' => 17, 'price' => 10000, 'supply_date' => '2025-07-20', 'created_at' => now(), 'updated_at' => now()], // Cà rốt
            ['supplier_id' => 1, 'ingredient_id' => 20, 'price' => 15000, 'supply_date' => '2025-07-20', 'created_at' => now(), 'updated_at' => now()], // Cải thảo
            ['supplier_id' => 1, 'ingredient_id' => 21, 'price' => 10000, 'supply_date' => '2025-07-20', 'created_at' => now(), 'updated_at' => now()], // Bắp
            ['supplier_id' => 1, 'ingredient_id' => 26, 'price' => 10000, 'supply_date' => '2025-07-20', 'created_at' => now(), 'updated_at' => now()], // Cải Vàng
            ['supplier_id' => 1, 'ingredient_id' => 29, 'price' => 250000, 'supply_date' => '2025-07-20', 'created_at' => now(), 'updated_at' => now()], // Thịt bò Mỹ

            // Nhà phân phối đồ khô Minh Tâm (supplier_id = 2)
            ['supplier_id' => 2, 'ingredient_id' => 1, 'price' => 15000, 'supply_date' => '2025-07-22', 'created_at' => now(), 'updated_at' => now()], // Mì Chinnoo
            ['supplier_id' => 2, 'ingredient_id' => 5, 'price' => 60000, 'supply_date' => '2025-07-22', 'created_at' => now(), 'updated_at' => now()], // Chả cá Hàn Quốc
            ['supplier_id' => 2, 'ingredient_id' => 6, 'price' => 50000, 'supply_date' => '2025-07-22', 'created_at' => now(), 'updated_at' => now()], // Cá viên
            ['supplier_id' => 2, 'ingredient_id' => 12, 'price' => 5000, 'supply_date' => '2025-07-22', 'created_at' => now(), 'updated_at' => now()], // Xúc xích
            ['supplier_id' => 2, 'ingredient_id' => 16, 'price' => 30000, 'supply_date' => '2025-07-22', 'created_at' => now(), 'updated_at' => now()], // Ớt chuông
            ['supplier_id' => 2, 'ingredient_id' => 18, 'price' => 10000, 'supply_date' => '2025-07-22', 'created_at' => now(), 'updated_at' => now()], // Hành baro
            ['supplier_id' => 2, 'ingredient_id' => 19, 'price' => 70000, 'supply_date' => '2025-07-22', 'created_at' => now(), 'updated_at' => now()], // Tokbokki
            ['supplier_id' => 2, 'ingredient_id' => 22, 'price' => 5000, 'supply_date' => '2025-07-22', 'created_at' => now(), 'updated_at' => now()], // Phô mai viên
            ['supplier_id' => 2, 'ingredient_id' => 25, 'price' => 30000, 'supply_date' => '2025-07-22', 'created_at' => now(), 'updated_at' => now()], // Kimbap
            ['supplier_id' => 2, 'ingredient_id' => 27, 'price' => 20000, 'supply_date' => '2025-07-22', 'created_at' => now(), 'updated_at' => now()], // Rong Biển
            ['supplier_id' => 2, 'ingredient_id' => 28, 'price' => 80000, 'supply_date' => '2025-07-22', 'created_at' => now(), 'updated_at' => now()], // Nước lẩu kim chi
            ['supplier_id' => 2, 'ingredient_id' => 30, 'price' => 60000, 'supply_date' => '2025-07-22', 'created_at' => now(), 'updated_at' => now()], // Bò viên
            ['supplier_id' => 2, 'ingredient_id' => 31, 'price' => 55000, 'supply_date' => '2025-07-22', 'created_at' => now(), 'updated_at' => now()], // Chả cá sợi

            // Công ty nước giải khát Thanh Bình (supplier_id = 3)
            ['supplier_id' => 3, 'ingredient_id' => 23, 'price' => 25000, 'supply_date' => '2025-07-25', 'created_at' => now(), 'updated_at' => now()], // Nước gạo
            ['supplier_id' => 3, 'ingredient_id' => 24, 'price' => 20000, 'supply_date' => '2025-07-25', 'created_at' => now(), 'updated_at' => now()], // Coca
        ]);
    }
}
