<?php

namespace App\Services;

use App\Models\Order;

class PointService
{
    public function updateUserPointsWhenOrderCompleted(Order $order)
    {
        $status = strtolower($order->order_status);
        if (!in_array($status, ['giao thành công', 'hoàn thành'])) {
        return;
}
        if ($order->points_awarded) {
            return;
        }
        $user = $order->user;
        if (!$user) {
            return;
        }
        $usablePointRate = 1000;
        $usablePoints = floor($order->total_price / $usablePointRate) * 0.5;
        $rankPoints = 3;

        $user->increment('usable_points', $usablePoints);
        $user->increment('rank_points', $rankPoints);

        $order->update(['points_awarded' => true]);
    }
}
