<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodPost;
use Illuminate\Http\Request;

class AdminFoodPost extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $foodPost = FoodPost::with(
            'foods',
            'user',

        )->get();
        return response()->json([
            'result' => $foodPost
        ]);
    }

    public function getPostById($id)
    {
        $data = FoodPost::find($id);
        return response()->json([
            'data' => $data
        ]);
    }

    public function updatePost(Request $request, $id)
    {
        $post = FoodPost::find($id);

        if (!$post) {
            return response()->json(['message' => 'Bài viết không tồn tại'], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'category' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB
        ]);

        $post->title = $request->title;
        $post->content = $request->content;
        $post->category = $request->category;

        if ($request->hasFile('image')) {
            // Tạo tên file mới
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();

            // Lưu file vào storage
            $request->image->storeAs('public/img/post', $imageName);

            // Cập nhật tên file vào DB
            $post->image = $imageName;
        }

        $post->save();

        return response()->json([
            'message' => 'Cập nhật bài viết thành công',
            'post' => $post
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'category' => 'required',
            // cho phép client gửi, kiểm tra tồn tại
            'user_id'  => 'nullable|integer|exists:users,id',
        ]);

        // Lấy user_id: ưu tiên auth()->id(), nếu không có thì lấy từ request
        $uid = auth()->id() ?: $request->input('user_id');

        if (!$uid) {
            return response()->json([
                'message' => 'Không xác định được user_id. Vui lòng đăng nhập hoặc gửi kèm user_id.'
            ], 422);
        }

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->storeAs('public/img/post', $imageName);
        }

        $post = FoodPost::create([
            'user_id'      => $uid,
            'title'        => $request->title,
            'content'      => $request->content,
            'image'        => $imageName,
            'category'     => $request->category,
            'published_at' => now(),
        ]);

        return response()->json([
            'message' => 'Thêm bài viết thành công',
            'post'    => $post,
        ]);
    }


    public function hidePost($id)
    {
        $post = FoodPost::find($id);
        $post->is_hidden = !$post->is_hidden;
        $post->save();

        return response()->json([
            'success' => true,
            'message' => $post->is_hidden ? 'Đã ẩn bài viết' : 'Đã hiện bài viết',
            'post' => $post
        ]);
    }
}
