<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function getAllCategories()
    {
        try {
            $cates = Category::with('children')->whereNull('parent_id')->get();
            return response()->json($cates);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi lấy danh sách danh mục', 'error' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $query = Category::with('children')
            ->whereNull('parent_id');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->parent_id) {
            $query->where('id', $request->parent_id);
        }

        $perPage = $request->input('per_page', 10);
        $categories = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json($categories);
    }


    // lấy danh mục cha
    public function getParents()
    {
        try {
            $parents = Category::whereNull('parent_id')->orderBy('name')->get();

            return response()->json([
                'message' => 'Lấy danh mục cha thành công',
                'data' => $parents
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi lấy danh mục cha',
                'error' => $e->getMessage()
            ], 500);
        }
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:categories,id',
                'images' => 'nullable|image|max:2048',
                'default' => 'required|boolean',
                // 2MB
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        }

        $imagePath = null;

        if ($request->hasFile('images')) {
            $image = $request->file('images');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/img/food/imgmenu', $imageName);
            $imagePath = $imageName;
        }

        $category = Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'images' => $imagePath,
            'default' => $request->default,

        ]);

        return response()->json([
            'message' => 'Thêm danh mục thành công',
            'data' => $category
        ], 201);
    }
    /**
     * Display the specified resource.
     */

    // lấy danh mục theo id
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Không tìm thấy danh mục'
            ], 404);
        }

        return response()->json([
            'data' => $category
        ]);
    }

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
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $id, // tránh gán cha là chính nó
            'default' => 'required|boolean',
            'images' => 'nullable|image|max:2048', // giới hạn ảnh 2MB
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'parent_id.exists' => 'Danh mục cha không tồn tại.',
            'parent_id.not_in' => 'Không thể chọn chính danh mục này làm danh mục cha.',
            'default.required' => 'Trạng thái mặc định là bắt buộc.',
            'default.boolean' => 'Trạng thái mặc định không hợp lệ.',
            'images.image' => 'Ảnh phải là tệp hình ảnh.',
            'images.max' => 'Ảnh không được vượt quá 2MB.',
        ]);

        $category->name = $request->name;
        $category->parent_id = $request->parent_id;
        $category->default = $request->default;

        // xử lý ảnh nếu có upload mới
        if ($request->hasFile('images')) {
            // xóa ảnh cũ nếu có
            if ($category->images && Storage::exists('public/img/food/imgmenu/' . $category->images)) {
                Storage::delete('public/img/food/imgmenu/' . $category->images);
            }

            $image = $request->file('images');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/img/food/imgmenu', $imageName);
            $category->images = $imageName;
        }

        $category->save();

        return response()->json([
            'message' => 'Cập nhật danh mục thành công!',
            'data' => $category
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Nếu là danh mục cha, cập nhật parent_id của con thành null
        if ($category->children()->count() > 0) {
            foreach ($category->children as $child) {
                $child->parent_id = null;
                $child->save();
            }
        }

        $category->delete(); // soft delete danh mục cha

        return response()->json(['message' => 'Đã xoá danh mục cha và cập nhật danh mục con']);
    }


    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Danh sách ID không hợp lệ'], 400);
        }

        try {
            Category::whereIn('id', $ids)->delete(); // hoặc softDelete()
            return response()->json(['message' => 'Xoá thành công']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi xoá danh mục'], 500);
        }
    }
}
