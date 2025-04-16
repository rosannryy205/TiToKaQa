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
                'code' => 'GIAM10',
                'name' => 'Giảm 10k',
                'discount_value' => 10000,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(7),
                'status' => 'active',
                'usage_limit' => 9999,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GIAM20',
                'name' => 'Giảm 20k',
                'discount_value' => 20000,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(10),
                'status' => 'active',
                'usage_limit' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GIAM30',
                'name' => 'Free Ship',
                'discount_value' => 30000,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(5),
                'status' => 'active',
                'usage_limit' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }}
