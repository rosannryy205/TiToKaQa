<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Services\PointService;
use Illuminate\Http\Request;
use Symfony\Contracts\Service\Attribute\Required;

class DiscountController extends Controller
{
    public function getAllDiscounts(Request $request)
    {
        try {
            $query = Discount::query();
            //lay theo source    
            if ($request->has('source')) {
                $query->where('source', $request->get('source'));
            } 
            //lay theo danh muc    
            if ($request->has('category_id')) {
                $query->where('category_id', $request->get('category_id'));
            }       
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
    
    public function createDiscounts(Request $request){

        $data = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
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
            'user_level' => 'nullable|in:new,silver,gold,diamond',
            'cost' => 'nullable|integer|min:0|required_if:source,point_exchange',
            'condition' => 'nullable|string|max:255',
            'custom_condition_note' => 'nullable|string|max:255',
        ]);
        try {
           $discount = Discount::create([
            
           ]);
        } catch (\Throwable $th) {
            //throw $th;
        }

    }

    
}
