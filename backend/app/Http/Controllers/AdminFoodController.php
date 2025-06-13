<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminFoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = (int) $request->input('limit', 10);
        // Default limit = 10
        $categoryId = $request->input('categoryId'); // Lấy giá trị categoryId từ request

        $query = Food::with('category')->orderBy('id', 'desc'); // Query cơ bản, bao gồm thông tin danh mục của món ăn

        // Nếu có categoryId, lọc món ăn theo categoryId và các danh mục con của nó
        if ($categoryId) {
            // Lấy tất cả danh mục con của categoryId
            $categoryIds = Category::where('parent_id', $categoryId)
                ->orWhere('id', $categoryId) // Bao gồm cả danh mục cha
                ->pluck('id'); // Lấy danh sách ID của danh mục con và cha

            // Lọc món ăn theo danh mục con và cha
            $query->whereIn('category_id', $categoryIds);
        }

        // Trả về kết quả theo phân trang
        return response()->json($query->paginate($limit));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'required|image|max:2048',
            'sale_price' => 'nullable|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:1000',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Tên món ăn là bắt buộc.',
            'price.required' => 'Giá món ăn là bắt buộc.',
            'price.numeric' => 'Giá món ăn phải là một số.',
            'image.required' => 'Hình ảnh là bắt buộc.',
            'image.image' => 'Vui lòng chọn một tệp hình ảnh hợp lệ.',
            'image.max' => 'Hình ảnh không được lớn hơn 2MB.',
            'stock.required' => 'Số lượng món ăn là bắt buộc.',
            'stock.integer' => 'Số lượng món ăn phải là một số nguyên.',
            'category_id.required' => 'Danh mục món ăn là bắt buộc.',
            'category_id.exists' => 'Danh mục món ăn không tồn tại.',
            'description.max' => 'Mô tả không được dài hơn 1000 ký tự.',
            'description.required' => 'Mô tả không được bỏ trống.',
            'status.required' => 'Trạng thái món ăn là bắt buộc.',
            'status.in' => 'Trạng thái phải là "active" hoặc "inactive".',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            $file->storeAs('public/img/food', $filename);
            $validated['image'] = $filename;
        }

        $food = Food::create($validated);

        return response()->json([
            'message' => 'Thêm món ăn thành công',
            'data' => $food
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function getFoodById($id)
    {
        try {
            $food = Food::find($id);
            if (!$food) {
                return response()->json(['message' => 'Không tìm thấy món ăn'], 404);
            }

            // Thêm đường dẫn ảnh đầy đủ nếu cần
            $food->image_url = $food->image_path ? asset('storage/' . $food->image_path) : null;

            return response()->json($food);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi lấy món ăn', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        // Tìm món ăn cần sửa
        $food = Food::find($id);

        if (!$food) {
            return response()->json(['message' => 'Món ăn không tồn tại.'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
            'sale_price' => 'nullable|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:1000',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Tên món ăn là bắt buộc.',
            'price.required' => 'Giá món ăn là bắt buộc.',
            'price.numeric' => 'Giá món ăn phải là một số.',
            'image.image' => 'Vui lòng chọn một tệp hình ảnh hợp lệ.',
            'image.max' => 'Hình ảnh không được lớn hơn 2MB.',
            'stock.required' => 'Số lượng món ăn là bắt buộc.',
            'stock.integer' => 'Số lượng món ăn phải là một số nguyên.',
            'category_id.required' => 'Danh mục món ăn là bắt buộc.',
            'category_id.exists' => 'Danh mục món ăn không tồn tại.',
            'description.max' => 'Mô tả không được dài hơn 1000 ký tự.',
            'description.required' => 'Mô tả không được bỏ trống.',
            'status.required' => 'Trạng thái món ăn là bắt buộc.',
            'status.in' => 'Trạng thái phải là "active" hoặc "inactive".',
        ]);

        // Nếu có ảnh mới, lưu ảnh mới và xoá ảnh cũ
        if ($request->hasFile('image')) {
            // Xoá ảnh cũ
            $oldImagePath = 'public/img/food/' . $food->image;
            if ($food->image && Storage::exists($oldImagePath)) {
                Storage::delete($oldImagePath);
            }

            // Lưu ảnh mới
            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/img/food', $filename);

            // Cập nhật tên ảnh trong request validated
            $validated['image'] = $filename;
        }


        // Cập nhật thông tin món ăn
        $food->update($validated);

        return response()->json([
            'message' => 'Sửa món ăn thành công.',
            'data' => $food
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $food = Food::find($id);
        if (!$food) {
            return response()->json(['message' => 'Món ăn không tồn tại.'], 404);
        }

        $imagePath = 'public/img/food/' . $food->image;

        if ($food->image && Storage::exists($imagePath)) {
            Storage::delete($imagePath);
        }
        $food->delete();
        return response()->json(['message' => 'Xóa món ăn thành công.'], 200);
    }

    public function getAllFood(){
        try {
            $allFood = Food::with('category')->get();
            return response()->json($allFood);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ],500);
        }
    }
    
    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Danh sách ID không hợp lệ'], 400);
        }

        Food::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Xoá thành công']);
    }
}
