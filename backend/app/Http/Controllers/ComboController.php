<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use Illuminate\Http\Request;

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
}
