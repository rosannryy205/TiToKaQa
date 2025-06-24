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
        $query = Category::with('parent'); // 👈 Load luôn quan hệ cha

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Sắp xếp mới nhất trước
        $query->orderByDesc('id');

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
                'type' => 'required|in:food,topping',
            ], [
                'name.required' => 'Tên danh mục là bắt buộc.',
                'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
                'parent_id.exists' => 'Danh mục cha không tồn tại.',
                'default.required' => 'Trạng thái mặc định là bắt buộc.',
                'default.boolean' => 'Trạng thái mặc định không hợp lệ.',
                'images.image' => 'Ảnh phải là tệp hình ảnh.',
                'images.max' => 'Ảnh không được vượt quá 2MB.',
                'type.required' => 'Loại danh mục là bắt buộc.',
                'type.in' => 'Loại danh mục không hợp lệ (chỉ food hoặc topping).',
            ]);

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
                'type' => $request->type,
            ]);

            return response()->json([
                'message' => 'Thêm danh mục thành công!',
                'data' => $category
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi thêm danh mục.',
                'error' => $e->getMessage()
            ], 500);
        }
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
        try {
            $category = Category::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:categories,id|not_in:' . $id,
                'default' => 'required|boolean',
                'images' => 'nullable|image|max:2048',
                'type' => 'required|in:food,topping',
            ], [
                'name.required' => 'Tên danh mục là bắt buộc.',
                'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
                'parent_id.exists' => 'Danh mục cha không tồn tại.',
                'parent_id.not_in' => 'Không thể chọn chính danh mục này làm danh mục cha.',
                'default.required' => 'Trạng thái mặc định là bắt buộc.',
                'default.boolean' => 'Trạng thái mặc định không hợp lệ.',
                'images.image' => 'Ảnh phải là tệp hình ảnh.',
                'images.max' => 'Ảnh không được vượt quá 2MB.',
                'type.required' => 'Loại danh mục là bắt buộc.',
                'type.in' => 'Loại danh mục không hợp lệ (chỉ food hoặc topping).',
            ]);

            $category->name = $request->name;
            $category->parent_id = $request->parent_id;
            $category->default = $request->default;
            $category->type = $request->type;

            // Nếu có ảnh mới, xử lý lưu và xóa ảnh cũ
            if ($request->hasFile('images')) {
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
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Danh mục không tồn tại.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi cập nhật danh mục.',
                'error' => $e->getMessage()
            ], 500);
        }
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
