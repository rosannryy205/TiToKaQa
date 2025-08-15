<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Services\PointService;
use Illuminate\Http\Request;
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
        $discount = Discount::find($request->discount_id);

        if (!$discount) {
            return response()->json(['message' => 'Không tìm thấy mã giảm giá.'], 404);
        }

        if ($discount->used >= $discount->usage_limit) {
            return response()->json(['message' => 'Mã giảm giá đã hết lượt sử dụng.'], 400);
        }

        $discount->increment('used');

        return response()->json(['message' => 'Cập nhật số lượt sử dụng thành công.']);
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
        ->withPivot(['point_used', 'exchanged_at', 'expiry_at', 'source'])
        ->orderBy('pivot_exchanged_at', 'desc')
        ->get(); 
        return response()->json($discounts);
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
