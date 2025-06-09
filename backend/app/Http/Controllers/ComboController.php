<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\Combo_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComboController extends Controller
{
   public function getAllCombos(){
    try {
    $combos = Combo::all();
    return response()->json($combos);
} catch (\Exception $e) {
    return response()->json(['message' => 'Lỗi khi lấy danh combo món ăn', 'error' => $e->getMessage()], 500);
}
}
   public function getComboById($id){
    try {
    $combos = Combo::with('Foods')->find($id);;
    return response()->json($combos);
} catch (\Exception $e) {
    return response()->json(['message' => 'Lỗi khi lấy chi tiết Combo', 'error' => $e->getMessage()], 500);
}
}
    public function createCombosByAdmin(Request $request){
      try {
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'image' => 'required|string|max:255',
        'description' => 'nullable|string',
        'combo_details' => 'required|array|min:1',
        'combo_details.*.food_id' => 'required|integer|exists:foods,id',
        'combo_details.*.quantity' => 'required|integer|min:1',
    ]);
    $combo = Combo::create([
        'name'=> $data['name'],
        'price'=> $data['price'],
        'image'=> $data['image'],
        'description'=> $data['description'],
    ]);
    foreach($data['combo_details'] as $detail){
        Combo_detail::create([
            'combo_id'=>$combo->id,
            'food_id'=>$detail["food_id"],
            'quantity'=>$detail["quantity"],
        ]);
    }
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
}
