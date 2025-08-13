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

    public function getToppingById(Request $request, String $id)
    {
        $toppingId = Topping::FindOrFail($id);

        return response()->json($toppingId);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id'
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
    public function update(Request $request, String $id)
    {
        try {
            $topping = Topping::find($id);
            $topping->name = $request->name;
            $topping->price = $request->price;
            $topping->category_id = $request->category_id;
            $topping->update();
            return response()->json([
                'message' => 'Cập nhật món ăn thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $topping = Topping::find($id);
        if (!$topping) {
            return response()->json(['message' => 'Topping không tồn tại.'], 404);
        }
        $topping->delete();
        return response()->json(['message' => 'Xóa topping thành công.'], 200);
    }
}
