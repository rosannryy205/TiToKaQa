<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getAllCategories()
    {
        try {
            $cates = Category::whereNull('parent_id')->get();
            return response()->json($cates);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi lấy danh sách danh mục', 'error' => $e->getMessage()], 500);
        }
    }
}
