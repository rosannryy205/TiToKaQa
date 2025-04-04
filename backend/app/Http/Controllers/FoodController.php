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
    public function getFoodById($id){
        try{
            $foods = Food::find($id);
            if($foods){
                return response()->json($foods, 200);
            }
            return response()->json(['mess'=> 'Không tìm thấy ID Food']);
        }catch (\Exception $e){
            return response()->json(['mess'=>'Lỗi khi lấy chi tiết món ăn', 'error'=> $e->getMessage()], 500);

        }

    }
}
