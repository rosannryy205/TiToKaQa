<?php
namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\FoodReward;
use Illuminate\Http\Request;
use App\Models\LuckyWheelPrize;
use App\Models\LuckyWheelSpin;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class LuckyWheelController extends Controller
{
    public function getPrizes()
    {
        return LuckyWheelPrize::all();
    }


    public function checkSpinStatus(Request $request)
{
    $user = $request->user();

    $alreadySpun = LuckyWheelSpin::where('user_id', $user->id)
        ->whereDate('spun_at', today())
        ->exists();

    return response()->json([
        'has_spun_today' => $alreadySpun,
        'remaining_spin' => $alreadySpun ? 0 : 1,
        'max_spin' => 1,
    ]);
}

public function spin(Request $request)
{
    $user = auth()->user();

    // $alreadySpun = LuckyWheelSpin::where('user_id', $user->id)
    //     ->whereDate('spun_at', today())
    //     ->exists();

    // if ($alreadySpun) {
    //     return response()->json(['message' => 'Bạn đã quay hôm nay rồi.'], 403);
    // }

    $prizes = LuckyWheelPrize::all();
    $weighted = [];

    foreach ($prizes as $prize) {
        for ($i = 0; $i < $prize->probability; $i++) {
            $weighted[] = $prize;
        }
    }

    if (empty($weighted)) {
        return response()->json(['message' => 'Không có phần thưởng khả dụng.'], 404);
    }

    $selected = $weighted[array_rand($weighted)];

    $spin = LuckyWheelSpin::create([
        'user_id' => $user->id,
        'lucky_wheel_prize_id' => $selected->id,
        'prize_name' => $selected->name,
        'prize_type' => $selected->type,
        'prize_data' => $selected->data,
        'spun_at' => now()
    ]);

    return response()->json([
        'message' => $selected->type === 'discount'
            ? 'Bạn đã nhận được mã giảm giá!'
            : 'Bạn đã quay trúng!',
        'spin_id' => $spin->id,
        'type' => $selected->type,
        'prize' => [
            'id' => (int) $selected->id,
            'name' => $selected->name,
            'type' => $selected->type,
            'probability' => $selected->probability,
            'data' => $selected->data,
        ],
    ]);
}




    
    public function getUserRewards(Request $request)
{
    try {
        $query = LuckyWheelSpin::query();
        $query->where('user_id', $request->user()->id);
        if ($request->has('prize_type')) {
            $query->where('prize_type', $request->get('prize_type'));
        }
        $query->orderBy('spun_at', 'desc');
        return response()->json($query->get());
    } catch (\Throwable $e) {
        return response()->json([
            'mess' => 'Lỗi khi lấy phần thưởng người dùng',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    
}
