<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Food;
use App\Models\FoodReward;
use App\Models\LuckyWheelSpin;
use App\Models\User;
use Illuminate\Http\Request;
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
        if ($spin->prize_type === 'tpoint') {
            $tpoint = $spin->prize_data['usable_points'];
            $user->usable_points += $tpoint;
            $user->save();
        }
        if ($spin->prize_type === 'food') {
            $food = Food::find($spin->prize_data['food_id'] ?? null);

            if (!$food) {
                return response()->json(['message' => 'Không tìm thấy món ăn.'], 404);
            }
            $reward = FoodReward::create([
                'user_id' => $user->id,
                'code' => strtoupper(Str::random(10)),
                'name' => $spin->prize_name,
                'food_id' => $spin->prize_data['food_id'] ?? null,
                'expired_at' => now()->addDays(7),
                'food_snapshot' => $food->toArray()
            ]);
            
            $reward->food = $food;
        }
        if ($spin->prize_type === 'discount') {
            $code = $spin->prize_data['code'] ?? null;
            if ($code) {
                $discount = Discount::where('code', $code)->first();
                $existing = $user->discounts()->where('discount_id', $discount->id)->first();
                if ($existing) {
                    // Gia hạn thêm 7 ngày kể từ hạn hiện tại (nếu còn), hoặc từ hiện tại
                    $currentExpiry = $existing->pivot->expiry_at ?? now();
                    $newExpiry = now()->lt($currentExpiry)
                        ? \Carbon\Carbon::parse($currentExpiry)->addDays(7)
                        : now()->addDays(7);
    
                    $user->discounts()->updateExistingPivot($discount->id, [
                        'expiry_at' => $newExpiry,
                    ]);
                } else {
                    $user->discounts()->attach($discount->id, [
                        'point_used' => 0,
                        'source' => 'lucky_wheel',
                        'exchanged_at' => now(),
                        'expiry_at' => now()->addDays(7),
                    ]);
                }
            }
        }
        $spin->update([
            'is_claimed' => true,
            'claimed_at' => now(),
        ]);
    
        return response()->json([
            'message' => 'Nhận quà thành công',
        'data' => [
            'type' => $spin->prize_type,
            'code' => $reward->code ?? null,
            'food_id' => $reward->food_id ?? null,
            'food_snapshot' => $reward->food_snapshot ?? null,
    ],
        ]);
    }

   
}
