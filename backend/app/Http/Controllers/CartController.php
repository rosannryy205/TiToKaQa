<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Topping;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function addToCart(Request $request, $id)
{
    $food =Food::with('toppings')->find($id);

    if(!$food){
        return response()->json([
            'mess' => 'Không tìm thấy sản phẩm'
        ],404);
    }

    $requesToppingId = $request->input('toppings',[]);


    //Lấy danh sách ID topping hợp lệ của món ăn
    $validToppingId = $food->toppings->pluck('id')->toArray();


    //Lấy danh sách ID topping hợp lệ của món ăn
    foreach( $requesToppingId as $toppingId){
        if(!in_array($toppingId, $validToppingId)){
            return response()->json([
                'mess' => "Topping không hợp lệ",
            ],422);
        }
    }

    return response()->json([
        'mess' => 'Sản phẩm đã được thêm vào giỏ hàng',
        'food' => $food,
        'toppings' =>Topping::whereIn('id',$requesToppingId)->get(),
    ]);
}

}
