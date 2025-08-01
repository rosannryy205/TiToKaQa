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

        // --- Tìm kiếm và lọc Foods ---
        $foodsQuery = Food::with('category')
            ->where('status', 'active'); // Lọc chỉ các món ăn active

        if ($keyword) {
            // Thêm điều kiện tìm kiếm theo tên nếu có keyword
            // Bạn có thể cân nhắc dùng name_ascii và hàm removeVietnameseTones nếu cần tìm kiếm không dấu
            $foodsQuery->where('name', 'like', "%{$keyword}%");
        }

        $foods = $foodsQuery->get()->map(function ($item) {
            $item->setAttribute('type', 'food');
            return $item;
        });


        // --- Tìm kiếm và lọc Combos ---
        $combosQuery = Combo::where('status', 'active'); // Lọc chỉ các combo active

        if ($keyword) {
            // Thêm điều kiện tìm kiếm theo tên nếu có keyword
            $combosQuery->where('name', 'like', "%{$keyword}%");
        }

        $combos = $combosQuery->get()->map(function ($item) {
            $item->setAttribute('type', 'combo');
            return $item;
        });


        // --- Gộp lại, sắp xếp và phân trang ---
        $allResults = $foods->concat($combos);
        $sortedResults = $allResults->sortBy('name')->values(); // Sắp xếp theo tên

        $pagedResults = $sortedResults->slice($offset, $limit)->values(); // Áp dụng offset và limit

        return response()->json([
            'results' => $pagedResults,
            'total' => $sortedResults->count() // Tổng số kết quả sau khi lọc và sắp xếp
        ]);
    }

    // protected function removeVietnameseTones($str)
    // {
    //     $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
    //     $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
    //     $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
    //     $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
    //     $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
    //     $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
    //     $str = preg_replace("/(đ)/", "d", $str);
    //     $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
    //     $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
    //     $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
    //     $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
    //     $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
    //     $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
    //     $str = preg_replace("/(Đ)/", "D", $str);

    //     // Loại bỏ các ký tự đặc biệt, giữ lại chữ cái và số
    //     $str = preg_replace('/[^A-Za-z0-9\s]/', '', $str);

    //     return $str;
    // }
}
