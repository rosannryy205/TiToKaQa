<?php

namespace App\Http\Controllers;

use App\Models\FoodReward;
use Illuminate\Http\Request;

class DealFoodsController extends Controller
{
    public function getDealsFood(Request $request)
    {
        $user = auth()->user();
    
        $availableDeals = FoodReward::where('user_id', $user->id)
            ->where('is_used', false)
            ->whereDoesntHave('orderDetails.order', function ($query) {
                $query->whereNotIn('order_status', ['Đã hủy', 'Giao thành công']);
                $query->orderBy('created_at', 'desc');
            })
            ->get();
    
        return response()->json([
            'status' => true,
            'data' => $availableDeals,
        ]);
    }
}
    
