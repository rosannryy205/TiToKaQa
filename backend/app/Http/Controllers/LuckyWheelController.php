<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\FoodReward;
use Illuminate\Http\Request;
use App\Models\LuckyWheelPrize;
use App\Models\LuckyWheelSpin;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LuckyWheelController extends Controller
{
    public function getPrizes(Request $request)
    {
        try {
            $query = LuckyWheelPrize::query();

            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            } else {
                $query->where('status', 'active');
            }
            $query->orderBy('created_at', 'desc');    
            return response()->json($query->get());
        } catch (\Throwable $e) {
            return response()->json(['mess' => 'Lỗi khi lấy quà', 'error' => $e->getMessage()], 500);
        }
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

<<<<<<< HEAD
        $alreadySpun = LuckyWheelSpin::where('user_id', $user->id)
            ->whereDate('spun_at', today())
            ->exists();

        if ($alreadySpun) {
            return response()->json(['message' => 'Bạn đã quay hôm nay rồi.'], 403);
        }
=======
        // $alreadySpun = LuckyWheelSpin::where('user_id', $user->id)
        //     ->whereDate('spun_at', today())
        //     ->exists();

        // if ($alreadySpun) {
        //     return response()->json(['message' => 'Bạn đã quay hôm nay rồi.'], 403);
        // }
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f

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

    public function createLuckyPrizeByAdmin(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => ['required', Rule::in(['discount', 'food', 'tpoint'])],
            'probability' => 'required|integer|between:1,100',
            'data'        => 'required|json',
            'status' => 'required|in:active,inactive',
        ]);
        try {
            $prize = LuckyWheelPrize::create($data);
            return response()->json([
                'message' => 'Tạo quà thành công',
                'data' => $prize
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi tạo quà',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getLuckyPrizeById($id)
    {
        try {
            $prize = LuckyWheelPrize::find($id);
            if (!$prize) {
                return response()->json([
                    'message' => 'Không tìm thấy quà'
                ], 404);
            }

            return response()->json([
                'data' => $prize
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Lỗi khi lấy chi tiết quà',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function updateLuckyPrizeByAdmin(Request $request, $id)
    {
        $prize = LuckyWheelPrize::find($id);
        if (!$prize) {
            return response()->json([
                'status' => false,
                'mess' => 'Khong tim thay qua can cap nhat'
            ], 404);
        }
        $type = strtolower((string) $request->input('type', ''));
        $data = $request->input('data');
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data = $decoded;
            }
        }
        if (!is_array($data)) {
            $data = [];
        }
        $baseRules = [
            'name'        => ['required', 'string', 'max:255'],
            'type'        => ['required', Rule::in(['discount', 'food', 'tpoint'])],
            'probability' => ['required', 'integer', 'min:0', 'max:100'],
        ];
        $validated = $request->validate($baseRules, [
            'name.required'        => 'Tên quà là bắt buộc.',
            'type.required'        => 'Loại quà là bắt buộc.',
            'type.in'              => 'Loại quà không hợp lệ.',
            'probability.required' => 'Xác suất là bắt buộc.',
            'probability.integer'  => 'Xác suất phải là số nguyên.',
            'probability.min'      => 'Xác suất tối thiểu là 0.',
            'probability.max'      => 'Xác suất tối đa là 100.',
        ]);
        if ($type === 'discount') {
            $code = $data['code'] ?? null;
            if (!$code) {
                return response()->json([
                    'status' => false,
                    'mess'   => 'Mã giảm giá là bắt buộc.',
                ], 422);
            }
            // Kiểm tra code tồn tại (tuỳ bạn có cột status/source hay không thì bổ sung where)
            $exists = DB::table('discounts')->where('code', $code)->exists();
            if (!$exists) {
                return response()->json([
                    'status' => false,
                    'mess'   => 'Mã giảm giá không hợp lệ.',
                ], 422);
            }
        } elseif ($type === 'food') {
            $foodId = isset($data['food_id']) ? (int) $data['food_id'] : null;
            if (!$foodId) {
                return response()->json([
                    'status' => false,
                    'mess'   => 'Món ăn là bắt buộc.',
                ], 422);
            }
            $exists = DB::table('foods')->where('id', $foodId)->exists();
            if (!$exists) {
                return response()->json([
                    'status' => false,
                    'mess'   => 'Món ăn không tồn tại.',
                ], 422);
            }

            $data['food_id'] = $foodId;
        } elseif ($type === 'tpoint') {
            $points = isset($data['usable_points']) ? (int) $data['usable_points'] : 0;
            if ($points < 1) {
                return response()->json([
                    'status' => false,
                    'mess'   => 'Điểm phải là số nguyên dương.',
                ], 422);
            }
            $data['usable_points'] = $points;
        }

        $prize->name        = $validated['name'];
        $prize->type        = $type;
        $prize->probability = (int) $validated['probability'];
        $prize->data        = $data;

        $prize->save();

        return response()->json([
            'status' => true,
            'mess'   => 'Cập nhật thành công.',
            'data'   => $prize->fresh(),
        ], 200);
    }
    public function setStatusPrizeByAdmin(Request $request, $id)
    {
        $prize = LuckyWheelPrize::findOrFail($id);
    
        $data = $request->validate([
            'status' => ['required', Rule::in(['active','inactive'])],
        ]);
    
        if ($prize->status === $data['status']) {
            return response()->json([
                'message' => 'Trạng thái không thay đổi',
                'data'    => $prize,
            ], 200);
        }
    
        $prize->status = $data['status'];
        $prize->save();
    
        return response()->json([
            'message' => $data['status'] === 'inactive' ? 'Đã ẩn quà' : 'Đã bật lại quà',
            'data'    => $prize->fresh(),
        ], 200);
    }
    
}
