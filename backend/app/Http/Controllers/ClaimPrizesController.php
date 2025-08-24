<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use App\Models\Discount;
use App\Models\Food;
use App\Models\FoodReward;
use App\Models\LuckyWheelSpin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClaimPrizesController extends Controller
{
      public function claimPrize(Request $request)
    {
        $user = $request->user();
        $reward = null;

        $request->validate([
            'spin_id' => 'required|exists:lucky_wheel_spins,id',
        ]);

        $spin = LuckyWheelSpin::where('id', $request->spin_id)
            ->where('user_id', $user->id)
            ->where('is_claimed', false)
            ->first();

        if (!$spin) {
            return response()->json(['message' => 'Phần thưởng không hợp lệ hoặc đã nhận.'], 400);
        }

        $prizeData = is_array($spin->prize_data)
            ? $spin->prize_data
            : json_decode($spin->prize_data ?? '[]', true);

        $extra = [];

        DB::beginTransaction();
        try {
            if ($spin->prize_type === 'tpoint') {
                $tpoint = (int)($prizeData['usable_points'] ?? 0);
                if ($tpoint <= 0) {
                    DB::rollBack();
                    return response()->json(['message' => 'Điểm thưởng không hợp lệ.'], 422);
                }
                $user->increment('usable_points', $tpoint);
            }

            if ($spin->prize_type === 'food') {
                $food = Food::find($prizeData['food_id'] ?? null);
                if (!$food) {
                    DB::rollBack();
                    return response()->json(['message' => 'Không tìm thấy món ăn.'], 404);
                }
                $reward = FoodReward::create([
                    'user_id'       => $user->id,
                    'code'          => strtoupper(Str::random(10)),
                    'name'          => $spin->prize_name,
                    'food_id'       => $prizeData['food_id'] ?? null,
                    'expired_at'    => now()->addDays(7),
                    'food_snapshot' => $food->toArray()
                ]);
            }

            if ($spin->prize_type === 'discount') {
                $code = $prizeData['code'] ?? null;
                if (!$code) {
                    DB::rollBack();
                    return response()->json(['message' => 'Thiếu mã giảm giá trong prize_data.'], 422);
                }

                $discount = Discount::where('code', $code)->first();
                if (!$discount) {
                    DB::rollBack();
                    return response()->json(['message' => 'Mã giảm giá không tồn tại.'], 404);
                }
                $existing = $user->discounts()
                    ->where('discounts.id', $discount->id)
                    ->first();

                if ($existing) {
                    $currentExpiry = optional($existing->pivot)->expiry_at
                        ? Carbon::parse($existing->pivot->expiry_at)
                        : now();

                    $newExpiry = now()->lt($currentExpiry)
                        ? $currentExpiry->copy()->addDays(7)
                        : now()->addDays(7);

                    $user->discounts()->updateExistingPivot($discount->id, [
                        'expiry_at'  => $newExpiry,
                        'updated_at' => now(),
                    ]);

                    $extra = [
                        'discount_code'   => $code,
                        'discount_id'     => $discount->id,
                        'discount_expiry' => $newExpiry->toDateTimeString(),
                    ];
                } else {
                    $expiry = now()->addDays(7);
                    $user->discounts()->attach($discount->id, [
                        'point_used'   => 0,
                        'source'       => 'lucky_wheel',
                        'exchanged_at' => now(),
                        'expiry_at'    => $expiry,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);

                    $extra = [
                        'discount_code'   => $code,
                        'discount_id'     => $discount->id,
                        'discount_expiry' => $expiry->toDateTimeString(),
                    ];
                }
            }

            $spin->update([
                'is_claimed' => true,
                'claimed_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Nhận quà thành công',
                'data' => array_merge([
                    'type' => $spin->prize_type,
                    'code' => $reward->code ?? null,
                    'food_id' => $reward->food_id ?? null,
                    'food_snapshot' => $reward->food_snapshot ?? null,
                ], $extra),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('claimPrize error', [
                'spin_id' => $request->spin_id,
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Có lỗi xảy ra khi nhận quà.'], 500);
        }
    }
}
