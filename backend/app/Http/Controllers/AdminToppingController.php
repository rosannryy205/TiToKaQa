<?php

namespace App\Http\Controllers;

use App\Models\Topping;
use App\Models\Category_Topping;
use Illuminate\Http\Request;

class AdminToppingController extends Controller
{
    public function index(Request $request)
    {
        $topping = Topping::all();
        return response()->json($topping);
    }
    public function getByCategoryId(Request $request)
    {
        $limit = $request->input('limit', 10);
        $categoryId = $request->input('categoryId');

        $query = Topping::with('category_topping')->orderBy('id', 'desc');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        return response()->json($query->paginate($limit));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:category_toppings,id'
        ], [
            'name.required' => 'Tên topping là bắt buộc',
            'price.required' => 'Giá món ăn là bắt buộc.',
            'price.numeric' => 'Giá món ăn phải là một số.',
            'category_id.required' => 'Danh mục món ăn là bắt buộc.',
            'category_id.exists' => 'Danh mục món ăn không tồn tại.',
        ]);

        try {
            $topping = new Topping();
            $topping->name = $request->name;
            $topping->price = $request->price;
            $topping->category_id = $request->category_id;
            $topping->save();
            return response()->json([
                'message' => 'Thêm món ăn thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
