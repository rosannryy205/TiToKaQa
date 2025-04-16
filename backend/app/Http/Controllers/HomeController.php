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

    public function searchPage($keyword)
    {
        $results = $this->searchAll($keyword, 0, 5);
        
        return response()->json([
            'keyword' => $keyword,
            'results' => $results
        ], $results->isEmpty() ? 404 : 200); // Nếu mảng trống, trả về mã lỗi 404
    }

    public function loadMore(Request $request)
    {
        $keyword = $request->input('keyword');
        $offset = (int) $request->input('offset', 0);

        $results = $this->searchAll($keyword, $offset, 5);

        return response()->json([
            'results' => $results
        ], $results->isEmpty() ? 404 : 200); // Nếu mảng trống, trả về mã lỗi 404
    }



    private function searchAll($keyword, $offset, $limit)
    {
        $foods = Food::where('name', 'like', "%$keyword%")
            ->get()
            ->map(function ($item) {
                $item->type = 'food';
                return $item;
            });

        $toppings = Topping::where('name', 'like', "%$keyword%")
            ->get()
            ->map(function ($item) {
                $item->type = 'topping';
                return $item;
            });

        $combos = Combo::where('name', 'like', "%$keyword%")
            ->get()
            ->map(function ($item) {
                $item->type = 'combo';
                return $item;
            });

        $merged = $foods->merge($toppings)->merge($combos)->sortBy('name')->values();

        return $merged->slice($offset, $limit)->values();
    }
}
