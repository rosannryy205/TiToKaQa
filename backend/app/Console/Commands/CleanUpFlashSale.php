<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanUpFlashSale extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:flashsale';

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
    
        $expiredFoods = \App\Models\Food::whereNotNull('flash_sale_end')
            ->where('flash_sale_end', '<=', $now)
            ->get();
    
        foreach ($expiredFoods as $food) {
            $sold = $food->flash_sale_sold ?? 0;
            $quantity = $food->flash_sale_quantity ?? 0;
            $unsold = $quantity - $sold;
    
            $food->increment('stock', $unsold);
            $food->increment('quantity_sold', $sold);
    
            $food->update([
                'flash_sale_price' => null,
                'flash_sale_quantity' => null,
                'flash_sale_sold' => 0,
                'flash_sale_start' => null,
                'flash_sale_end' => null,
            ]);
    
            $this->info("🧹 Kết thúc Flash Sale cho {$food->name} (Bán: {$sold}, Trả lại: {$unsold})");
        }
    
        $this->info("✅ Đã dọn dẹp các món hết thời gian Flash Sale.");
    }
    
}
