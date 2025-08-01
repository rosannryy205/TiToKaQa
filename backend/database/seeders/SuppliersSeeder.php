<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuppliersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('suppliers')->insert([
            [
                'id' => 1,
                'name' => 'Cửa hàng thực phẩm tươi sống An Phát',
                'address' => '123 Đường ABC, Quận 1, TP.HCM',
                'phone' => '0901234567',
                'email' => 'anphat@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Nhà phân phối đồ khô Minh Tâm',
                'address' => '456 Đường XYZ, Quận Bình Thạnh, TP.HCM',
                'phone' => '0908765432',
                'email' => 'minhtam@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Công ty nước giải khát Thanh Bình',
                'address' => '789 Đường DEF, TP.Thủ Đức, TP.HCM',
                'phone' => '0912345678',
                'email' => 'thanhbinh@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
