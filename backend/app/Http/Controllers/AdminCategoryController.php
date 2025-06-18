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
        $query = Category::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->parent_id !== null) {
            $query->where('parent_id', $request->parent_id);
        }

        // Ưu tiên danh mục cha (parent_id null), sau đó là con, rồi theo id tăng dần
        $query->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('parent_id')
            ->orderBy('id');

        $perPage = $request->input('per_page', 10);
        $categories = $query->paginate($perPage);

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

        // Tìm danh mục mặc định
        $defaultCategory = Category::where('default', 1)->first();

        if (!$defaultCategory) {
            return response()->json(['message' => 'Không tìm thấy danh mục mặc định'], 404);
        }

        if ($category->id == $defaultCategory->id) {
            return response()->json(['message' => 'Không thể xoá danh mục mặc định'], 400);
        }

        // Cập nhật các danh mục con về danh mục mặc định
        if ($category->children()->count() > 0) {
            foreach ($category->children as $child) {
                $child->parent_id = $defaultCategory->id;
                $child->save();
            }
        }

        // Cập nhật các món ăn thuộc danh mục bị xoá sang danh mục mặc định
        \App\Models\Food::where('category_id', $category->id)->update([
            'category_id' => $defaultCategory->id
        ]);

        // Xoá mềm danh mục
        $category->delete();

        return response()->json(['message' => 'Đã xoá danh mục và chuyển món ăn sang danh mục mặc định']);
    }



    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Danh sách ID không hợp lệ'], 400);
        }

        // Tìm danh mục mặc định
        $defaultCategory = Category::where('default', 1)->first();
        if (!$defaultCategory) {
            return response()->json(['message' => 'Không tìm thấy danh mục mặc định'], 404);
        }

        $deletedCount = 0;

        foreach ($ids as $id) {
            $category = Category::find($id);

            // Nếu không tìm thấy hoặc là danh mục mặc định thì bỏ qua
            if (!$category || $category->id == $defaultCategory->id) {
                continue;
            }

            // Cập nhật danh mục con về danh mục mặc định
            Category::where('parent_id', $category->id)->update([
                'parent_id' => $defaultCategory->id
            ]);

            // Cập nhật các món ăn về danh mục mặc định
            \App\Models\Food::where('category_id', $category->id)->update([
                'category_id' => $defaultCategory->id
            ]);

            // Xoá mềm danh mục
            $category->delete();
            $deletedCount++;
        }

        return response()->json([
            'message' => "Đã xoá {$deletedCount} danh mục và chuyển dữ liệu về danh mục mặc định"
        ]);
    }
}
