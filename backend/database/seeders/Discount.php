<?php

namespace Database\Seeders;

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
                'code' => 'matest',
                'name' => 'Mã để test',
                'discount_value' => 10,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'status' => 'active',
                'usage_limit' => 100
            ]
        ]);

    }
}
