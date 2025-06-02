<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Discount extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('discounts')->insert([
            [
                'code' => 'GIAM10KSHIP', 
                'name' => 'Giảm 10k phí vận chuyển cho đơn từ 50k',
                'discount_value' => 10000, 
                'discount_method' => 'fixed',
                'discount_type' => 'freeship', 
                'max_discount_amount' => null,
                'min_order_value' => 50000, 
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(7),
                'status' => 'active',
                'usage_limit' => 9999,
                'used' => 0, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GIAM5PTMAX20K',
                'name' => 'Giảm 5% tối đa 20k cho đơn hàng đồ ăn từ 100k',
                'discount_value' => 5, 
                'discount_method' => 'percent',
                'discount_type' => 'salefood', 
                'max_discount_amount' => 20000, 
                'min_order_value' => 100000,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(10),
                'status' => 'active',
                'usage_limit' => 500,
                'used' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Các voucher mới
            [
                'code' => 'SALEFOOD20PTMAX50K',
                'name' => 'Giảm 20% tối đa 50k cho đơn đồ ăn từ 150k',
                'discount_value' => 20, 
                'discount_method' => 'percent',
                'discount_type' => 'salefood',
                'max_discount_amount' => 50000, 
                'min_order_value' => 150000, 
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(30),
                'status' => 'active',
                'usage_limit' => 1000,
                'used' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'FREESHIPDON100K',
                'name' => 'Miễn phí vận chuyển (tối đa 15k) cho đơn từ 100k',
                'discount_value' => 15000, 
                'discount_method' => 'fixed',
                'discount_type' => 'freeship',
                'max_discount_amount' => 15000,                 
                'min_order_value' => 100000, 
                'start_date' => Carbon::now()->subDays(3), 
                'end_date' => Carbon::now()->addDays(15),
                'status' => 'active',
                'usage_limit' => 2000,
                'used' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GIAMNGAY30K',
                'name' => 'Giảm ngay 30k cho đơn hàng từ 200k',
                'discount_value' => 30000, 
                'discount_method' => 'fixed',
                'discount_type' => 'salefood',
                'max_discount_amount' => null, 
                'min_order_value' => 200000,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(1),
                'status' => 'active',
                'usage_limit' => 700,
                'used' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'NOELVUIVE',
                'name' => 'Giảm 15% tối đa 70k mùa noel (không cần đơn tối thiểu)',
                'discount_value' => 15, 
                'discount_method' => 'percent',
                'discount_type' => 'salefood',
                'max_discount_amount' => 70000, 
                'min_order_value' => 0, 
                'start_date' => Carbon::parse('2025-12-01'), 
                'end_date' => Carbon::parse('2025-12-25')->endOfDay(), 
                'status' => 'active',
                'usage_limit' => 1000,
                'used' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DAUHE50K',
                'name' => 'Giảm 50k cho đơn hàng trên 250k (đã hết hạn)',
                'discount_value' => 50000,
                'discount_method' => 'fixed',
                'discount_type' => 'salefood',
                'max_discount_amount' => null,
                'min_order_value' => 250000,
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->subMonth(), 
                'status' => 'inactive', 
                'usage_limit' => 500,
                'used' => 150, 
                'created_at' => Carbon::now()->subMonths(2),
                'updated_at' => Carbon::now()->subMonth(),
            ]
        ]);
    }
}
