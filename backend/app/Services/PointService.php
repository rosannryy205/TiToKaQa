<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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

    public function redeemDiscount(User $user, int $discountId)
    {
        $discount = Discount::find($discountId);

        if (!$discount) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Mã giảm giá không tồn tại.'
            ], 404);
        }

        // Kiểm tra điểm
        $requiredPoints = $discount->cost ?? 0;
        if ($user->usable_points < $requiredPoints) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Bạn không đủ TCoin để đổi mã này.'
            ], 400);
        }
        $already = DB::table('discount_user')
            ->where('user_id', $user->id)
            ->where('discount_id', $discount->id)
            ->first();

        if ($already) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Bạn đã đổi mã này rồi. Vui lòng kiểm tra kho của bạn!'
            ], 409);
        }

        DB::beginTransaction();
        try {
            $now = now();
            if ($discount->source === 'system' && $discount->end_date < $now) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mã giảm giá đã hết hạn!',
                ]);
            }
            $source = $discount->source === 'system' ? 'discount' : 'tpoint';
            $expiry = ($discount->source === 'system' && $discount->end_date)
                ? $discount->end_date
                : $now->copy()->addDays(7);
            $user->decrement('usable_points', $requiredPoints);
            DB::table('discount_user')->insert([
                'user_id'     => $user->id,
                'discount_id' => $discount->id,
                'point_used'  => $discount->cost,
                'source'       => $source,
                'exchanged_at' => $now,
                'expiry_at'   => $expiry,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Đổi mã thành công!',
                'data' => $discount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi đổi voucher.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
