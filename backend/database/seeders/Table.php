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
            ['capacity' => 2, 'table_number' => 1],
            ['capacity' => 2, 'table_number' => 2],
            ['capacity' => 2, 'table_number' => 3],
            ['capacity' => 2, 'table_number' => 4],
            ['capacity' => 2, 'table_number' => 5],
            ['capacity' => 2, 'table_number' => 6],
            ['capacity' => 2, 'table_number' => 7],
            ['capacity' => 2, 'table_number' => 8],
            ['capacity' => 2, 'table_number' => 9],
            ['capacity' => 2, 'table_number' => 10],
            ['capacity' => 2, 'table_number' => 11],
            ['capacity' => 2, 'table_number' => 12],

            ['capacity' => 4, 'table_number' => 13],
            ['capacity' => 4, 'table_number' => 14],
            ['capacity' => 4, 'table_number' => 15],
            ['capacity' => 4, 'table_number' => 16],
            ['capacity' => 4, 'table_number' => 17],
            ['capacity' => 4, 'table_number' => 18],
            ['capacity' => 4, 'table_number' => 19],
            ['capacity' => 4, 'table_number' => 20],
            ['capacity' => 4, 'table_number' => 21],

            ['capacity' => 6, 'table_number' => 22],
            ['capacity' => 6, 'table_number' => 23],
            ['capacity' => 6, 'table_number' => 24],
            ['capacity' => 6, 'table_number' => 25],
            ['capacity' => 6, 'table_number' => 26],
            ['capacity' => 6, 'table_number' => 27],
            ['capacity' => 6, 'table_number' => 28],
            ['capacity' => 6, 'table_number' => 29],
            ['capacity' => 6, 'table_number' => 30],
        ]);
    }
}
