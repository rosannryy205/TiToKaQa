<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\Food;
use App\Models\Topping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function api()
    {
        return response()->json('hello');
    }
    public function index()
    {
        $foods = Food::all();
        return response()->json($foods);
    }


    // public function search(Request $request)
    // {
    //     $q = $request->input('q');

    //     // Nếu không có từ khóa tìm kiếm thì không trả về gì cả
    //     if (!$q || trim($q) === '') {
    //         return response()->json([
    //             'combos' => [],
    //             'foods' => [],
    //             'toppings' => [],
    //         ]);
    //     }

    //     // Lấy offset mỗi bảng (mặc định là 0 nếu chưa gửi)
    //     $comboOffset = $request->input('combo_offset', 0);
    //     $foodOffset = $request->input('food_offset', 0);
    //     $toppingOffset = $request->input('topping_offset', 0);

    //     // Tìm trong bảng combos
    //     $combos = Combo::where('name', 'like', "%$q%")
    //         ->offset($comboOffset)
    //         ->limit(5)
    //         ->get();

    //     // Tìm trong bảng foods
    //     $foods = Food::where('name', 'like', "%$q%")
    //         ->offset($foodOffset)
    //         ->limit(5)
    //         ->get();

    //     // Tìm trong bảng toppings
    //     $toppings = Topping::where('name', 'like', "%$q%")
    //         ->offset($toppingOffset)
    //         ->limit(5)
    //         ->get();

    //     // Trả về JSON
    //     return response()->json([
    //         'combos' => $combos,
    //         'foods' => $foods,
    //         'toppings' => $toppings,
    //     ]);
    // }

    public function search(Request $request)
    {
        $keyword = $request->input('search');
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 5);

        // Lấy foods có liên kết category
        $foods = Food::with('category')
            ->where('name', 'like', "%$keyword%")
            ->get()
            ->map(function ($item) {
                $item->setAttribute('type', 'food');
                return $item;
            });

        // Lấy combos KHÔNG có category
        $combos = Combo::where('name', 'like', "%$keyword%")
            ->get()
            ->map(function ($item) {
                $item->setAttribute('type', 'combo');
                return $item;
            });

        // Gộp lại và phân trang
        $all = $foods->concat($combos);
        $sorted = $all->sortBy('name')->values();
        $paged = $sorted->slice($offset, $limit)->values();

        return response()->json([
            'results' => $paged,
            'total' => $sorted->count()
        ]);
    }
}
