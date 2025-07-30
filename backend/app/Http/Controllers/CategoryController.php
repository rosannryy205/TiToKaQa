<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getParentCategories()
    {
        try {
            $cates = Category::with('children')
            ->whereNull('parent_id')
            ->where('type', 'food')
            ->get();
            return response()->json($cates);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi lấy danh sách danh mục', 'error' => $e->getMessage()], 500);
        }
    }
        public function getAllCategories()
{
    return response()->json(Category::all());
}
    
}
