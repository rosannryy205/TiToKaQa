<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\Combo_detail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ComboController extends Controller
{
    public function getAllCombos()
    {
        try {
            $combos = Combo::with('foods')->get();
            return response()->json($combos);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi lấy danh combo món ăn', 'error' => $e->getMessage()], 500);
        }
    }
    public function getComboById($id)
    {
        try {
            $combos = Combo::with('Foods')->find($id);;
            return response()->json($combos);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi lấy chi tiết Combo', 'error' => $e->getMessage()], 500);
        }
    }
    public function createCombosByAdmin(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
                'description' => 'nullable|string',
                'combo_details' => 'required|array|min:1',
                'combo_details.*.food_id' => 'required|integer|exists:foods,id',
                'combo_details.*.quantity' => 'required|integer|min:1',
            ]);
            $existingCombo = Combo::where('name', $data['name'])->first();
            if ($existingCombo) {
                return response()->json([
                    'error' => 'Tên combo đã tồn tại'
                ], 422);
            }

            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $laravelPath = public_path('img/food');
            $image->move($laravelPath, $filename);
            $vuePublicPath = base_path('../vuejs/public/img/food');

            if (!file_exists($vuePublicPath)) {
                mkdir($vuePublicPath, 0755, true);
            }
            copy($laravelPath . '/' . $filename, $vuePublicPath . '/' . $filename);



            $combo = Combo::create([
                'name' => $data['name'],
                'price' => $data['price'],
                'image' => $filename,
                'description' => $data['description'],
            ]);
            foreach ($data['combo_details'] as $detail) {
                Combo_detail::create([
                    'combo_id' => $combo->id,
                    'food_id' => $detail["food_id"],
                    'quantity' => $detail["quantity"],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Tạo combo thành công',
                'combo_id' => $combo->id,
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => 'Đã xảy ra lỗi khi tạo combo',
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function updateCombosForAdmin(Request $request, $id)
{
    try {
        $combo = Combo::findOrFail($id);

        $combo->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $laravelPath = public_path('img/food');
            $image->move($laravelPath, $filename);
            $vuePublicPath = base_path('../vuejs/public/img/food');
            if (!file_exists($vuePublicPath)) {
                mkdir($vuePublicPath, 0755, true);
            }
            copy($laravelPath . '/' . $filename, $vuePublicPath . '/' . $filename);
            $combo->image = $filename;
            $combo->save();
        }
        $currentFoods = $combo->foods()->pluck('combo_details.quantity', 'foods.id')->toArray();

        $newFoods = collect(json_decode($request->foods, true));
        $newFoodIds = [];

        foreach ($newFoods as $food) {
            $foodId = $food['id'];
            $quantity = $food['quantity'];
            $newFoodIds[] = $foodId;

            if (isset($currentFoods[$foodId])) {
                if ($currentFoods[$foodId] != $quantity) {
                    $combo->foods()->updateExistingPivot($foodId, ['quantity' => $quantity]);
                }
            } else {
                $combo->foods()->attach($foodId, ['quantity' => $quantity]);
            }
        }
        $foodsToRemove = array_diff(array_keys($currentFoods), $newFoodIds);
        if (!empty($foodsToRemove)) {
            $combo->foods()->detach($foodsToRemove);
        }
        return response()->json(['message' => 'Cập nhật combo thành công']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Cập nhật combo thất bại', 'error' => $e->getMessage()], 500);
    }
}
public function deleteCombosForAdmin($id)
{
    try {
        $combo = Combo::findOrFail($id);
        $comboUsedInOrders  = DB::table('order_details')
            ->where('combo_id', $id)
            ->exists();
            if ($comboUsedInOrders) {
                return response()->json([
                    'message' => 'Không thể xóa combo vì đang được sử dụng trong đơn hàng.'
                ], 400);
            }
        if ($combo->image) {
            $laravelImagePath = public_path('img/food/' . $combo->image);
            $vueImagePath = base_path('../vuejs/public/img/food/' . $combo->image);

            if (file_exists($laravelImagePath)) {
                if (!unlink($laravelImagePath)) {
                    Log::error("Không xóa được ảnh Laravel: $laravelImagePath");
                }
            } else {
                Log::warning("Ảnh Laravel không tồn tại: $laravelImagePath");
            }

            if (file_exists($vueImagePath)) {
                if (!unlink($vueImagePath)) {
                    Log::error("Không xóa được ảnh Vue: $vueImagePath");
                }
            } else {
                Log::warning("Ảnh Vue không tồn tại: $vueImagePath");
            }
        }
        $combo->foods()->detach();
        $combo->delete();

        return response()->json(['message' => 'Xóa combo thành công']);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Xóa combo thất bại',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
