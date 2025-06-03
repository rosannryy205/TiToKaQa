<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function getAllDiscounts()
    {
        try {
            $discounts = Discount::all();
            return response()->json($discounts);
        } catch (\Throwable $e) {
            return response()->json(['mess' => 'Lỗi khi lấy chi tiết món ăn', 'error' => $e->getMessage()], 500);
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

}
