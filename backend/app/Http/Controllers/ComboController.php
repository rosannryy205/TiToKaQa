<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\Combo_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ComboController extends Controller
{
    public function getAllCombosForAdmin(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 1); 
            $query = Combo::query()->with('foods');
    
            if ($request->has('search') && !empty($request->search)) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
    
            $combos = $query->orderBy('created_at', 'desc')->paginate($perPage);
    
            return response()->json($combos);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi lấy danh combo món ăn', 
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getAllCombos(Request $request)
    {
        try {
          $combos = Combo::with('foods')
          ->where('status', 'active')
          ->orderBy('created_at', 'desc')
          ->get();
            return response()->json($combos);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi lấy danh combo món ăn', 'error' => $e->getMessage()], 500);
        }
    }

    public function getComboById($id)
    {
        try {
            $combos = Combo::with('foods')->find($id);
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

            if (Combo::where('name', $data['name'])->exists()) {
                return response()->json(['error' => 'Tên combo đã tồn tại'], 422);
            }

            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/img/food', $filename);

            $combo = Combo::create([
                'name' => $data['name'],
                'price' => $data['price'],
                'image' => $filename,
                'description' => $data['description'],
            ]);

            foreach ($data['combo_details'] as $detail) {
                Combo_detail::create([
                    'combo_id' => $combo->id,
                    'food_id' => $detail['food_id'],
                    'quantity' => $detail['quantity'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Tạo combo thành công', 'combo_id' => $combo->id], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => 'Lỗi khi tạo combo', 'message' => $th->getMessage()], 500);
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
                $image->storeAs('public/img/food', $filename);
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

    public function toggleStatusComboForAdmin($id)
    {
        try {
            $combo = Combo::findOrFail($id);
            $comboUsed = DB::table('order_details')->where('combo_id', $id)->exists();
            if ($comboUsed && $combo->status === 'active') {
                return response()->json(['message' => 'Combo đang được dùng trong đơn hàng, không thể ẩn'], 400);
            }
            $combo->status = $combo->status === 'active' ? 'inactive' : 'active';
            $combo->save();

            return response()->json([
                'message' => $combo->status === 'inactive' ? 'Đã ẩn combo' : 'Combo đã được hiển thị',
                'status' => $combo->status
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Cập nhật trạng thái combo thất bại', 'error' => $e->getMessage()], 500);
        }
    }

}
