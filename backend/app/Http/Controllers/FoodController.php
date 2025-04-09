<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function getAllFoods()
    {
        try {
            $foods = Food::all();
            return response()->json($foods);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi lấy danh sách món ăn', 'error' => $e->getMessage()], 500);
        }
    }
    public function getFoodById($id)
    {
        try {
            $foods = Food::find($id);
            if ($foods) {
                return response()->json($foods, 200);
            }
            return response()->json(['mess' => 'Không tìm thấy ID Food']);
        } catch (\Exception $e) {
            return response()->json(['mess' => 'Lỗi khi lấy chi tiết món ăn', 'error' => $e->getMessage()], 500);
        }
    }
    public function getFoodByCategory($id)
    {
        $foods = Food::where('category_id', $id)->get();
        return response()->json($foods);
    }

    // public function getToppingByFood($id)
    // {
    //     $food = Food::with('toppings')->find($id);
    //     return response()->json($food->toppings);
    //                 $query->with('toppings'); // lấy cả thông tin topping gốc nếu cần
    //     }])->find($id);

    //     return response()->json($food->food_toppings); // trả về bảng trung gian
    // }
    public function getToppingByFood($id)
    {
       $food = Food::with('toppings')->find($id);
        return response()->json($food->toppings);

    }


}
