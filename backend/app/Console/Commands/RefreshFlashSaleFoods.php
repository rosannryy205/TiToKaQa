<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Food;
use App\Models\FlashSaleFood;
use Carbon\Carbon;

class RefreshFlashSaleFoods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-flash-sale-foods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset danh sách món ăn Flash Sale mỗi khung giờ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $nextHour = $now->copy()->addHour()->startOfHour();
        $flashSales = FlashSaleFood::all();
        foreach ($flashSales as $sale) {
            $unsold = $sale->quantity_limit - ($sale->quantity_sold ?? 0);
    
            if ($unsold > 0) {
                $food = Food::find($sale->food_id);
                if ($food) {
                    $food->stock += $unsold;
                    $food->save();
    
                    $this->info("🔁 Trả lại {$unsold} stock cho {$food->name}");
                }
            }
        }
        FlashSaleFood::truncate();
        $this->info("🧹 Đã xoá toàn bộ flash sale cũ");

        $randomFoods = Food::inRandomOrder()->limit(5)->get();
        foreach ($randomFoods as $food) {
            $salePrice = $food->price * 0.7;
            $limit = 10;
    
            if ($food->stock < $limit) {
                $this->warn("⚠️ Không đủ stock cho {$food->name}, bỏ qua.");
                continue;
            }
    
            $food->stock -= $limit;
            $food->save();
    
            FlashSaleFood::create([
                'food_id' => $food->id,
                'original_price' => $food->price,
                'sale_price' => $salePrice,
                'start_time' => $now,
                'end_time' => $nextHour,
                'quantity_limit' => $limit,
            ]);
    
            $this->info("🔥 Món Flash Sale: {$food->name} - giảm còn {$salePrice}đ");
        }
    
        $this->info('✅ Flash Sale đã được reset thành công.');
    }
    
}
