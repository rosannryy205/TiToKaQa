<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\DiscountUser;
use App\Models\Order;
use App\Services\PointService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Contracts\Service\Attribute\Required;

class DiscountController extends Controller
{
    public function getAllDiscounts(Request $request)
    {
        try {
            $query = Discount::query();

            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            } else {
                $query->where('status', 'active');
            }
            //lay theo source
            if ($request->has('source')) {
                $query->where('source', $request->get('source'));
            }
            //lay theo danh muc
            if ($request->has('category_id')) {
                $query->where('category_id', $request->get('category_id'));
            }
            $query->orderBy('created_at', 'desc');
            return response()->json($query->get());
        } catch (\Throwable $e) {
            return response()->json(['mess' => 'Lỗi khi lấy mã giảm giá', 'error' => $e->getMessage()], 500);
        }
    }
 
    public function used(Request $request)
    {
        $data = $request->validate([
            'discount_id'       => 'nullable|exists:discounts,id',
            'discount_user_id'  => 'nullable|exists:discount_user,id',
            'order_id'          => 'nullable|exists:orders,id',
        ]);
    
        if (($request->filled('discount_id') && $request->filled('discount_user_id'))
            || (!$request->filled('discount_id') && !$request->filled('discount_user_id'))) {
            return response()->json([
                'message' => 'Chỉ được chọn 1 loại voucher: discount_id hoặc discount_user_id.'
            ], 422);
        }
    
        return DB::transaction(function () use ($request) {
            if ($request->filled('discount_user_id')) {
                $du = DiscountUser::with('discount')->lockForUpdate()->find($request->discount_user_id);
                if (!$du || !$du->discount) {
                    return response()->json(['message' => 'Voucher của bạn không hợp lệ.'], 404);
                }
                if ($du->expiry_at && Carbon::now()->gt(Carbon::parse($du->expiry_at))) {
                    return response()->json(['message' => 'Voucher của bạn đã hết hạn.'], 400);
                }
                if ($request->filled('order_id')) {
                    Order::where('id', $request->order_id)->update([
                        'discount_user_id' => $du->id,
                        'discount_id'      => null,
                    ]);
                }
                $affected = DB::table('discounts')
                    ->where('id', $du->discount_id)
                    ->whereRaw('used < usage_limit')
                    ->update(['used' => DB::raw('used + 1')]);
    
                if ($affected === 0) {
                    return response()->json(['message' => 'Voucher đã hết lượt sử dụng.'], 400);
                }
    
                return response()->json(['message' => 'Cập nhật số lượt sử dụng thành công.']);
            }
    
            // ===== Trường hợp mã public (discount_id) =====
            if ($request->filled('discount_id')) {
                $discount = Discount::lockForUpdate()->find($request->discount_id);
                if (!$discount) {
                    return response()->json(['message' => 'Không tìm thấy mã giảm giá.'], 404);
                }
                if ($request->filled('order_id')) {
                    Order::where('id', $request->order_id)->update([
                        'discount_id'      => $discount->id,
                        'discount_user_id' => null,
                    ]);
                }
                $affected = DB::table('discounts')
                    ->where('id', $discount->id)
                    ->whereRaw('used < usage_limit')
                    ->update(['used' => DB::raw('used + 1')]);
    
                if ($affected === 0) {
                    return response()->json(['message' => 'Mã giảm giá đã hết lượt sử dụng.'], 400);
                }
    
                return response()->json(['message' => 'Cập nhật số lượt sử dụng thành công.']);
            }
            return response()->json(['message' => 'Yêu cầu không hợp lệ.'], 422);
        });
    }
    
    public function redeem(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'discount_id' => 'required|integer|exists:discounts,id'
        ]);
        $service = new PointService();
        return $service->redeemDiscount($user, $request->discount_id);
    }

    public function getDiscountByCategory(Request $request)
{
    $query = Discount::query();

    if ($request->has('category_id') && $request->category_id !== null) {
        $query->where('category_id', $request->category_id);
    }
    $query->where('source', 'point_exchange');

    return response()->json($query->get());
}

public function getUserDiscounts(Request $request)
{
    $user = auth()->user();

    $discounts = $user->discounts()
        ->withPivot(['id', 'point_used', 'exchanged_at', 'expiry_at', 'source'])
        ->orderByPivot('exchanged_at', 'desc') 
        ->get();
    $rows = $discounts->map(function ($d) {
        return [
            'discount_user_id' => $d->pivot->id, 
            'discount_id'      => $d->id,        
            'code'             => $d->code,
            'name'             => $d->name,
            'discount_type'    => $d->discount_type,
            'discount_method'  => $d->discount_method,
            'discount_value'   => $d->discount_value,
            'min_order_value'  => $d->min_order_value,
            'end_date'         => $d->end_date,
            'expiry_at'        => $d->pivot->expiry_at,
            'point_used'       => $d->pivot->point_used,
            'source'           => $d->pivot->source,
        ];
    });

    return response()->json($rows);
}


    public function getDiscountById($id)
{
    try {
        $discount = Discount::find($id);

        if (!$discount) {
            return response()->json([
                'message' => 'Không tìm thấy mã giảm giá'
            ], 404);
        }

        return response()->json([
            'data' => $discount
        ], 200);
    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Lỗi khi lấy chi tiết mã giảm giá',
            'error'   => $e->getMessage()
        ], 500);
    }
}


    public function createDiscounts(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:discounts,code',
            'name' => 'required|string|max:255',
            'discount_value' => 'required|integer|min:1',
            'discount_method' => 'required|in:percent,fixed',
            'discount_type' => 'required|in:freeship,salefood',
            'max_discount_amount' => 'nullable|integer|min:1|required_if:discount_method,percent',
            'min_order_value' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive',
            'usage_limit' => 'required|integer|min:1',
            'source' => 'required|in:system,point_exchange,lucky_wheel,for_users',


            'user_level' => 'nullable|in:new,silver,gold,diamond|required_if:source,for_users',


            'cost' => 'nullable|integer|min:0|required_if:source,point_exchange',

            'condition' => 'nullable|string|max:255',
            'custom_condition_note' => 'nullable|string|max:255',
        ]);

        try {
            $discount = Discount::create($data);

            return response()->json([
                'message' => 'Tạo mã giảm giá thành công',
                'data' => $discount
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi tạo mã giảm giá',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function updateDiscountByAdmin(Request $request, $id)
    {
        try {
            $discount = Discount::findOrFail($id);

            // 1) Chuẩn hoá field rỗng -> null để qua được 'nullable'
            $input = $request->all();
            foreach (['category_id','start_date','end_date','max_discount_amount','min_order_value','user_level','cost','condition','custom_condition_note'] as $f) {
                if (array_key_exists($f, $input) && ($input[$f] === '' || $input[$f] === 'null')) {
                    $input[$f] = null;
                }
            }
            // Gộp lại vào request để validate dựa trên dữ liệu đã chuẩn hoá
            $request->merge($input);

            // 2) Validate – giữ nguyên rule như create, nhưng 'code' unique bỏ qua chính nó
            $data = $request->validate([
                'code' => [
                    'required','string','max:50',
                    Rule::unique('discounts','code')->ignore($discount->id),
                ],
                'name' => 'required|string|max:255',
                'discount_value' => 'required|integer|min:1',
                'discount_method' => 'required|in:percent,fixed',
                'discount_type' => 'required|in:freeship,salefood',

                'max_discount_amount' => 'nullable|integer|min:1|required_if:discount_method,percent',
                'min_order_value'     => 'nullable|integer|min:0',

                'category_id' => 'nullable|exists:categories,id',

                'start_date' => 'nullable|date',
                'end_date'   => 'nullable|date|after_or_equal:start_date',

                'status'      => 'required|in:active,inactive',
                'usage_limit' => 'required|integer|min:1',

                'source' => 'required|in:system,point_exchange,lucky_wheel,for_users',

                'user_level' => 'nullable|in:new,silver,gold,diamond|required_if:source,for_users',
                'cost'       => 'nullable|integer|min:0|required_if:source,point_exchange',

                'condition'             => 'nullable|string|max:255',
                'custom_condition_note' => 'nullable|string|max:255',
            ]);

            // 3) Bảo vệ logic required_if phía server
            if (($data['discount_method'] ?? $discount->discount_method) !== 'percent') {
                $data['max_discount_amount'] = null;
            }
            if (($data['source'] ?? $discount->source) !== 'for_users') {
                $data['user_level'] = null;
            }
            if (($data['source'] ?? $discount->source) !== 'point_exchange') {
                $data['cost'] = null;
            }

            // 4) Update
            $discount->update($data);
            return response()->json([
                'message' => 'Cập nhật mã giảm giá thành công',
                'data'    => $discount->fresh(),
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật mã giảm giá',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }



    public function setStatusByAdmin(Request $request, $id)
{
    {
        $discount = Discount::findOrFail($id);

        $data = $request->validate([
            'status' => ['required', Rule::in(['active','inactive'])],
        ]);

        if ($discount->status === $data['status']) {
            return response()->json([
                'message' => 'Trạng thái không thay đổi',
                'data'    => $discount,
            ], 200);
        }

        $discount->status = $data['status'];
        $discount->save();

        return response()->json([
            'message' => $data['status'] === 'inactive' ? 'Đã ẩn quà' : 'Đã bật lại quà',
            'data'    => $discount->fresh(),
        ], 200);
    }
}
}
