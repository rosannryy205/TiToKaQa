<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }



    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users|min:6|max:20|alpha_num',

            'email' => [
                'required',
                'email',
                'unique:users',
                function ($attribute, $value, $fail) {
                    if (!checkdnsrr(array_slice(explode("@", $value), -1)[0], "MX")) {
                        $fail("Tên miền email không tồn tại.");
                    }
                }
            ],

            'password' => 'required|confirmed|min:6',

        ], [
            // Thông báo tiếng Việt cho từng rule
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'username.unique' => 'Tên đăng nhập đã tồn tại.',
            'username.min' => 'Tên đăng nhập phải có ít nhất 6 ký tự.',
            'username.max' => 'Tên đăng nhập không được vượt quá 20 ký tự.',
            'username.alpha_num' => 'Tên đăng nhập chỉ có thể chứa chữ cái và số.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã được sử dụng.',

            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'address' => $request->address ?? '',
            'fullname' => $request->fullname ?? '',
        ]);
        $token = $user->createToken('auth')->plainTextToken;

        // Gửi mail chào mừng
        Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));

        return response()->json(['message' => 'Đăng ký thành công!', 'user' => $user,'token'=>$token]);
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required', // email hoặc username
            'password' => 'required',
        ], [
            'login.required' => 'Vui lòng nhập email hoặc tên đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $request->login,
            'password' => $request->password
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Thông tin đăng nhập không chính xác.'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth')->plainTextToken;
        return response()->json([
            'message' => 'Đăng nhập thành công!',
            'user' => $user,
            'token'=>$token
        ]);
    }


    public function logout(Request $request)
    {
        // Hủy token hiện tại của người dùng
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công!'
        ]);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
