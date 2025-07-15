<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Category_topping;
use App\Models\Topping;
use Illuminate\Http\Request;

class AdminCategoryToppingController extends Controller
{
    public function index(){
        try {
            $cates = Category::all();
            return response()->json($cates);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi lấy danh sách danh mục', 'error' => $e->getMessage()], 500);
        }
    }



}
