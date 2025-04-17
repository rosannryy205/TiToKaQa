<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
   public function getAllDiscounts(){
try {
    $discounts = Discount::all();
    return response()->json($discounts);
} catch (\Throwable $e) {
    return response()->json(['mess' => 'Lỗi khi lấy chi tiết món ăn', 'error' => $e->getMessage()], 500);
}
  
    

}
}
