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
                'name' => 'Giảm 10k ',
                'discount_value' => 10000,
                'discount_method' => 'fixed',
                'discount_type' => 'freeship',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(7),
                'status' => 'active',
                'usage_limit' => 9999,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GIAM5%',
                'name' => 'Giảm 5% ',
                'discount_value' => 5,
                'discount_method' => 'percent',
                'discount_type' => 'salefood',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(7),
                'status' => 'active',
                'usage_limit' => 9999,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }}
