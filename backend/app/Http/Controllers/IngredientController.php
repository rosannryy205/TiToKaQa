<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
     public function index()
    {
        // Sử dụng paginate() để lấy 10 nguyên liệu mỗi trang
        $ingredients = Ingredient::orderBy('id', 'desc')->paginate(10);

        // Trả về dữ liệu dưới dạng JSON. Laravel sẽ tự động thêm thông tin phân trang.
        return response()->json([
            'success' => true,
            'message' => 'Danh sách nguyên liệu đã được tải thành công.',
            'data' => $ingredients 
        ]);
    }
}
