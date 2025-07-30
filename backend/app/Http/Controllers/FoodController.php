<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{

    //healper
    // function removeVietnameseTones($str) {
    //     $str = preg_replace([
    //         "/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/u",
    //         "/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/u",
    //         "/(ì|í|ị|ỉ|ĩ)/u",
    //         "/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/u",
    //         "/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/u",
    //         "/(ỳ|ý|ỵ|ỷ|ỹ)/u",
    //         "/(đ)/u",
    //         "/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/u",
    //         "/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/u",
    //         "/(Ì|Í|Ị|Ỉ|Ĩ)/u",
    //         "/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/u",
    //         "/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/u",
    //         "/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/u",
    //         "/(Đ)/u"
    //     ], [
    //         "a","e","i","o","u","y","d",
    //         "A","E","I","O","U","Y","D"
    //     ], $str);

    //     return $str;
    // }

    public function getAllFoods()
    {
        try {
            $foods = Food::where('status', 'active')->with('category')->get();
            return response()->json($foods);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi lấy danh sách món ăn', 'error' => $e->getMessage()], 500);
        }
    }
    public function getFoodById($id)
    {
        try {
            $foods = Food::find($id);
            if ($foods) {
                return response()->json($foods, 200);
            }
            return response()->json(['mess' => 'Không tìm thấy ID Food']);
        } catch (\Exception $e) {
            return response()->json(['mess' => 'Lỗi khi lấy chi tiết món ăn', 'error' => $e->getMessage()], 500);
        }
    }
    public function getFoodByCategory($id)
    {
        $foods = Food::where('category_id', $id)->get();
        return response()->json($foods);
    }

    public function getToppingByFood($id)
    {
        $food = Food::with('toppings')->find($id);
        return response()->json($food->toppings);
    }

    public function search(Request $request)
    {
        $search = $request->query('search');

        if (!$search) {
            return response()->json(Food::with('category')->get());
        }
        $normalizedSearch = strtolower($this->removeVietnameseTones($search));
        $keywords = explode(' ', $normalizedSearch);

        $foods = Food::with('category')->get()->filter(function ($food) use ($keywords) {
            $normalizedFoodName = strtolower($this->removeVietnameseTones($food->name_ascii));
            foreach ($keywords as $keyword) {
                if (strpos($normalizedFoodName, $keyword) === false) {
                    return false;
                }
            }
            return true;
        });

        $result = $foods->map(function ($food) {
            return [
                'id' => $food->id,
                'name' => $food->name,
                'description' => $food->description,
                'price' => $food->price,
                'image' => $food->image,
                'category_name' => optional($food->category)->name ?? 'Món Ăn',
                'category_image' => optional($food->category)->image ?? 'mycay.png',
            ];
        });

        return response()->json($result->values());
    }


    private function removeVietnameseTones($str)
    {
        $unicode = [
            'a' => ['à', 'á', 'ạ', 'ả', 'ã'],
            'e' => ['è', 'é', 'ẹ', 'ẻ', 'ẽ'],
            'i' => ['ì', 'í', 'ị', 'ỉ', 'ĩ'],
            'o' => ['ò', 'ó', 'ọ', 'ỏ', 'õ'],
            'u' => ['ù', 'ú', 'ụ', 'ủ', 'ũ'],
            'y' => ['ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ'],
        ];

        foreach ($unicode as $nonUnicode => $uniChars) {
            $str = str_replace($uniChars, $nonUnicode, $str);
        }
        return $str;
    }

    public function getFlashSaleFoods()
{
    $now = now();

    $foods = Food::whereNotNull('flash_sale_price')
        ->where('flash_sale_start', '<=', $now)
        ->where('flash_sale_end', '>=', $now)
        ->get();

    return response()->json([
        'success' => true,
        'data' => $foods,
    ]);
}
}
