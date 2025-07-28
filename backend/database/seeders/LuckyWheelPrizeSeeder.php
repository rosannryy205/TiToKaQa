<?php

namespace Database\Seeders;

use App\Models\LuckyWheelPrize;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LuckyWheelPrizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prizes = [

        
            ['name' => 'Deal Coca size L',        'type' => 'food',     'data' => ['food_id' => 20],           'probability' => 10],
            ['name' => 'Deal KimBap Chiên',        'type' => 'food',     'data' => ['food_id' => 21],           'probability' => 10],
            ['name' => 'Deal Mỳ Cay Kim Chi',          'type' => 'food',     'data' => ['food_id' => 9],            'probability' => 10],
        
            ['name' => '3K T-Point',              'type' => 'tpoint',   'data' => ['usable_points' => 3000],   'probability' => 10],
        
            ['name' => 'Mã LUCKY5K',              'type' => 'discount', 'data' => ['code' => 'LUCKY5K'],       'probability' => 10],
            ['name' => 'Mã FREESHIP',             'type' => 'discount', 'data' => ['code' => 'LUCKYFREESHIP'], 'probability' => 10],
            ['name' => 'Mã 10% OFF',              'type' => 'discount', 'data' => ['code' => 'LUCKY10PERCENT'],'probability' => 10],
            ['name' => 'Mã 50% OFF',              'type' => 'discount', 'data' => ['code' => 'LUCKY50PERCENT'],'probability' => 10],
            ['name' => 'Mã FREE SHIP VIP',        'type' => 'discount', 'data' => ['code' => 'LUCKYSHIPFREE'], 'probability' => 10],
            ['name' => 'Mã 90K ĐƠN LỚN',          'type' => 'discount', 'data' => ['code' => 'LUCKY90K'],      'probability' => 10],
        ];
        
        

        foreach ($prizes as $prize) {
            LuckyWheelPrize::create($prize);
        }
    }
}
