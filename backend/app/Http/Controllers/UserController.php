<?php

namespace App\Http\Controllers;

use App;
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
        $user = User::all();
        return $user;
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
            'phone' => ['required', 'regex:/^(0|\+84)(\d{9})$/', 'unique:users,phone'],

            'password' => 'required|confirmed|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',

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

            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
            'phone.unique' => 'Số điện thoại đã được sử dụng.',

            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'password.regex' => 'Mật khẩu phải có ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone ?? '',
            'password' => bcrypt($request->password),
            'address' => $request->address ?? '',
            'fullname' => $request->fullname ?? '',
            'role' => 'user'

        ]);
        Auth::login($user);
        $token = $user->createToken('auth')->plainTextToken;

        // Gửi mail chào mừng
        // Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));

        return response()->json([
            'message' => 'Đăng ký thành công!',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role
            ],
            'token' => $token
        ]);
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
        /** @var \App\Models\User $user */
        $token = $user->createToken('auth')->plainTextToken;
        return response()->json([
            'message' => 'Đăng nhập thành công!',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role
            ],
            'token' => $token
        ]);
    }


    public function logout(Request $request)
    {
        // Hủy token hiện tại của người dùng
        /** @var \App\Models\User $user */
        $user = Auth::user();

        /** @var \Laravel\Sanctum\PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Đăng xuất thành công!'
        ]);
    }


    public function forgotPass(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email'
            ],
            [
                'email.required' => "vui lòng nhập địa chỉ email",
                'email.email' => 'Địa chỉ email không hợp lệ'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'errors' => ['email' => ['Email không tồn tại trong hệ thống']]
            ], 404);
        }

        $code = rand(100000, 999999);
        // $code = strtoupper(Str::random(6)); có chữ
        $user->verify_code = $code;
        $user->verify_expiry = now()->addMinutes(5);
        $user->save();

        try {
            Mail::to($user->email)->send(new \App\Mail\ResetPasswordCode($code));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi gửi email',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Mã xác nhận đã được gửi qua email.',
            'email_expired_at' => $user->verify_expiry
        ]);
    }

    public function sendCode(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email'
            ],
            [
                'email.required' => "vui lòng nhập địa chỉ email",
                'email.email' => 'Địa chỉ email không hợp lệ'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'errors' => ['email' => ['Email không tồn tại trong hệ thống']]
            ], 404);
        }

        $code = rand(100000, 999999);
        // $code = strtoupper(Str::random(6)); có chữ
        $user->verify_code = $code;
        $user->verify_expiry = now()->addMinutes(5);
        $user->save();

        try {
            Mail::to($user->email)->send(new \App\Mail\ResetPasswordCode($code));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi gửi email',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Mã xác nhận đã được gửi qua email.',
            'email_expired_at' => $user->verify_expiry
        ]);
    }


    public function verifyResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required'
        ], [
            'email.required' => 'Vui lòng nhập email',
            'code.required' => 'Vui lòng nhập mã khôi phục'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $user = User::where('email', $request->email)
            ->where('verify_code', $request->code)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Mã xác minh không đúng'], 404);
        }

        if (now()->greaterThan($user->verify_expiry)) {
            return response()->json(['message' => 'Mã xác minh đã hết hạn'], 410);
        }

        $user->verify_code = null;
        $user->verify_expiry = null;
        $user->save();

        return response()->json(['message' => 'Xác minh thành công']);
    }


    public function ChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'password.regex' => 'Mật khẩu phải có ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['message' => 'Đặt lại mật khẩu thành công']);
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
        $user = User::find($id);
        return response()->json($user);
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

        $user = User::findOrFail($id);

        $user->fullname = $request->input('fullname');
        $user->phone = $request->input('phone');
        $user->address = $request->input('address');

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/avatar'), $filename);
            $user->avatar = $filename;
        }
        $user->save();

        return response()->json([
            'message' => 'Đã cập nhật user thành công!',
            'user' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
