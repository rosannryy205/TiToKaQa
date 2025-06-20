<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Services\PointService;
use Illuminate\Http\Request;

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
        ->withPivot(['point_used', 'exchanged_at', 'expiry_at'])
        ->get(); 
        return response()->json($discounts);
    }
    

    
}
