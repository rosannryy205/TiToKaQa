<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Table extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tables')->insert([
            [
                'capacity' => 2,
                'table_number' => 1
            ],
            [
                'capacity' => 4,
                'table_number' => 2
            ],
            [
                'capacity' => 6,
                'table_number' => 3
            ],
            [
                'capacity' => 2,
                'table_number' => 4
            ],
            [
                'capacity' => 4,
                'table_number' => 5
            ],
            [
                'capacity' => 6,
                'table_number' => 6
            ],
            [
                'capacity' => 2,
                'table_number' => 7
            ],
            [
                'capacity' => 4,
                'table_number' => 8
            ],
            [
                'capacity' => 6,
                'table_number' => 9
            ],
            [
                'capacity' => 2,
                'table_number' => 10
            ],
        ]);

    }
}
