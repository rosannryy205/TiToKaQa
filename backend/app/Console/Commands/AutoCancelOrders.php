<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;

class AutoCancelOrders extends Command
{
    protected $signature = 'orders:auto-cancel';
    protected $description = 'Tự động huỷ đơn nếu đến expiration_time mà chưa check-in';

    public function handle()
    {
        $now = Carbon::now();

        $orders = Order::whereNull('check_in_time')
            ->where('expiration_time', '<=', $now)
            ->where('reservation_status', '!=', 'Đã huỷ')
            ->get();

        foreach ($orders as $order) {
            $order->reservation_status = 'Đã huỷ';
            $order->save();
        }

        $this->info("Đã huỷ " . $orders->count() . " đơn quá hạn.");
    }
}
