<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupFlashSale extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:flashsale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $end = $now->copy()->addHour(); 
        $limit = 10;
    
        $foods = \App\Models\Food::where('stock', '>=', $limit)
            ->inRandomOrder()
            ->limit(10)
            ->get();
    
        foreach ($foods as $food) {
            $flashQuantity = min($food->stock, $limit);

            $food->decrement('stock', $flashQuantity);
    
            $food->update([
                'flash_sale_price' => $food->price * 0.7,
                'flash_sale_quantity' => $flashQuantity,
                'flash_sale_sold' => 0,
                'flash_sale_start' => $now,
                'flash_sale_end' => $end,
            ]);
    
            $this->info("🔥 {$food->name} đã được chọn Flash Sale với {$flashQuantity} SP đến {$end->format('H:i')}");
        }
    
        $this->info("✅ Đã setup Flash Sale cho {$foods->count()} món ăn.");
    }
    
}
