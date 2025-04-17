<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
Carbon::setLocale('vi');
date_default_timezone_set('Asia/Ho_Chi_Minh');
class AutoCancelOrders extends Command
{
    protected $signature = 'orders:auto-cancel';
    protected $description = 'Tự động huỷ đơn nếu đến expiration_time mà chưa check-in';

    public function handle()
    {
        Log::info('🔁 AutoCancelOrders is running at: ' . now());

        $now = Carbon::now();

        $orders = Order::whereNull('check_in_time')
            ->where('expiration_time', '<=', $now)
            ->where('reservation_status', '!=', 'Đã hủy')
            ->get();

        foreach ($orders as $order) {
            $order->reservation_status = 'Đã hủy';
            $order->save();
            Log::info('❌ Đơn bị huỷ: ' . $order->id);
        }

        $this->info("Đã huỷ " . $orders->count() . " đơn quá hạn.");
    }
}
