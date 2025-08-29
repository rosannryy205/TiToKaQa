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
    
        $request->validate([
            'spin_id' => 'required|exists:lucky_wheel_spins,id',
        ]);
    
        return DB::transaction(function () use ($request, $user) {
            $spin = LuckyWheelSpin::where('id', $request->spin_id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();
    
            if (!$spin || $spin->is_claimed) {
                return response()->json(['message' => 'Phần thưởng không hợp lệ hoặc đã nhận.'], 400);
            }
    
            $prizeData = is_array($spin->prize_data)
                ? $spin->prize_data
                : json_decode($spin->prize_data ?? '[]', true);
    
            $payload = ['type' => $spin->prize_type];
            $extra   = [];
    
            // ---- TPOINT 
            if ($spin->prize_type === 'tpoint') {
                $tpoint = (int) ($prizeData['usable_points'] ?? 0);
                if ($tpoint <= 0) {
                    return response()->json(['message' => 'Điểm thưởng không hợp lệ.'], 422);
                }
                $user->increment('usable_points', $tpoint);
                $extra['points_added'] = $tpoint;
            }
    
            // ---- FOOD REWARD 
            if ($spin->prize_type === 'food') {
                $foodId = $prizeData['food_id'] ?? null;
                $food   = $foodId ? Food::find($foodId) : null;
                if (!$food) {
                    return response()->json(['message' => 'Không tìm thấy món ăn.'], 404);
                }
    
                $reward = FoodReward::create([
                    'user_id'       => $user->id,
                    'code'          => strtoupper(Str::random(10)),
                    'name'          => $spin->prize_name,
                    'food_id'       => $food->id,
                    'spin_id'       => $spin->id,              
                    'source'        => 'lucky_wheel',
                    'expired_at'    => now()->addDays(7),
                    'food_snapshot' => [
                        'id'          => $food->id,
                        'name'        => $food->name,
                        'image'       => $food->image ?? null,
                        'price_at_win'=> $food->price ?? null,
                        'category_id' => $food->category_id ?? null,
                    ],
                ]);
    
                $payload['reward_id']     = $reward->id;
                $payload['code']          = $reward->code;
                $payload['food_id']       = $reward->food_id;
                $payload['food_snapshot'] = $reward->food_snapshot;
                $extra['expired_at']      = Carbon::parse($reward->expired_at)->toDateTimeString();
            }
    
            // ---- DISCOUNT (voucher) 
            if ($spin->prize_type === 'discount') {
                // Có thể cấu hình prize bằng code hoặc discount_id
                $code        = $prizeData['code']        ?? null;
                $discountId  = $prizeData['discount_id'] ?? null;
    
                $discount = null;
                if ($discountId) {
                    $discount = Discount::find($discountId);
                } elseif ($code) {
                    $discount = Discount::where('code', $code)->first();
                }
    
                if (!$discount) {
                    return response()->json(['message' => 'Mã giảm giá không tồn tại.'], 404);
                }
                $expiry = now()->addDays(7);
                $user->discounts()->attach($discount->id, [
                    'point_used'   => 0,
                    'source'       => 'lucky_wheel',
                    'spin_id'      => $spin->id,    
                    'exchanged_at' => now(),
                    'expiry_at'    => $expiry,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
    
                $payload['discount_id']     = $discount->id;
                $payload['discount_code']   = $discount->code;
                $extra['discount_expiry']   = $expiry->toDateTimeString();
            }
            $spin->update([
                'is_claimed' => true,
                'claimed_at' => now(),
            ]);
    
            return response()->json([
                'message' => 'Nhận quà thành công',
                'data'    => array_merge($payload, $extra),
            ]);
        }, 3);
    }
    
}
