<?php

namespace App\Http\Controllers;

use App;
use App\Mail\QuickRegisterMail;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\error;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('orders')->get();

        $usersWithRoles = $users->map(function ($user) {
            return [
                'user' => $user,
                'roles' => $user->getRoleNames(),
            ];
        });

        return response()->json([
            'user' => $usersWithRoles
        ]);
    }

    public function sendRegisterCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|max:20|alpha_num|unique:users',

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

            'password' => 'required|confirmed|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',

        ], [
            // Thông báo tiếng Việt cho từng rule
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'username.max' => 'Tên đăng nhập không được vượt quá 20 ký tự.',
            'username.alpha_num' => 'Tên đăng nhập chỉ có thể chứa chữ cái và số.',
            'username.unique' => 'Tên đăng nhập đã được sử dụng.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã được sử dụng.',

            // 'phone.required' => 'Vui lòng nhập số điện thoại.',
            // 'phone.regex' => 'Số điện thoại không đúng định dạng.',
            // 'phone.unique' => 'Số điện thoại đã được sử dụng.',

            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'password.regex' => 'Mật khẩu phải có ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Sinh mã và lưu tạm thông tin user trong cache
        $code = rand(100000, 999999);
        $data = [
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'address' => $request->address ?? '',
            'fullname' => $request->fullname ?? '',

        ];


        Cache::put('register_' . $request->email, [
            'code' => $code,
            'data' => $data,
        ], now()->addMinutes(5));

        try {
            Mail::to($request->email)->send(new \App\Mail\ResetPasswordCode($code));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi gửi email',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json(['message' => 'Mã xác minh đã được gửi đến email của bạn.']);
    }

    public function verifyRegisterCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|numeric',
        ]);

        $cached = Cache::get('register_' . $request->email);

        if (!$cached) {
            return response()->json(['message' => 'Mã đã hết hạn hoặc không tồn tại'], 410);
        }

        if ($cached['code'] != $request->code) {
            return response()->json(['message' => 'Mã xác minh không đúng'], 404);
        }

        // Tạo user và đăng nhập
        $user = User::create(array_merge($cached['data']));
        $user->assignRole('khachhang');
        Auth::login($user);
        $token = $user->createToken('auth')->plainTextToken;

        /**xuly tang dc cho nguoi moi */
        $newUserDiscounts = Discount::where('user_level', 'new')
            ->where('status', 'active')
            ->where('source', 'for_users')
            ->get();
        $data = [];
        $issuedAt  = now();
        $expiryAt  = now()->addDays(7);
        foreach ($newUserDiscounts as $discount) {
            $data[$discount->id] = [
                'point_used' => 0,
                'exchanged_at' => $issuedAt,
                'expiry_at' => $expiryAt,
                'used_at' => null,
                'source' => 'register_reward',
                'created_at' => $issuedAt,
                'updated_at' => $issuedAt,
            ];
        }
        $user->discounts()->attach($data);

        // Gửi mail chào mừng
        Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));

        Cache::forget('register_' . $request->email);

        return response()->json([
            'message' => 'Đăng ký thành công!',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first()
            ],
            'token' => $token,
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


        // if ($user->status === 'Block') {
        //     Auth::logout();
        //     return response()->json([
        //         'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.'
        //     ], 403);
        // }


        /** @var \App\Models\User $user */
        $token = $user->createToken('auth')->plainTextToken;
        return response()->json([
            'message' => 'Đăng nhập thành công!',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->getRoleNames()->first()
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


    public function quickRegister(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã được sử dụng',
        ]);

        try {
            // tạo mật khẩu mạnh
            $password = $this->generateStrongPassword(8);

            // lấy name từ email
            $nameFromEmail = explode('@', $request->email)[0];
            $name = ucfirst(substr($nameFromEmail, 0, 10));

            // tạo user
            $user = User::create([
                'username' => $name,
                'email' => $request->email,
                'password' => bcrypt($password),
            ]);

            try {
                // gửi mail
                Mail::to($user->email)->send(new QuickRegisterMail($user->username, $user->email, $password));

                return response()->json([
                    'status' => 'success',
                    'message' => 'Đăng ký thành công. Vui lòng kiểm tra email để lấy thông tin đăng nhập.',
                ]);
            } catch (\Exception $e) {
                // gửi mail lỗi nhưng user đã tạo
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Tài khoản đã được tạo nhưng gửi email thất bại.',
                    'error' => $e->getMessage() // thêm thông tin lỗi chi tiết
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đăng ký thất bại. Vui lòng thử lại sau.',
                'error' => $e->getMessage() // thêm thông tin lỗi chi tiết
            ], 500);
        }
    }



    private function generateStrongPassword($length = 8)
    {
        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $special = '@$!%*?&';

        // đảm bảo có ít nhất 1 ký tự thuộc mỗi nhóm
        $all = $lower . $upper . $numbers . $special;
        $password = $lower[rand(0, strlen($lower) - 1)]
            . $upper[rand(0, strlen($upper) - 1)]
            . $numbers[rand(0, strlen($numbers) - 1)]
            . $special[rand(0, strlen($special) - 1)];

        // thêm các ký tự còn lại ngẫu nhiên
        for ($i = 4; $i < $length; $i++) {
            $password .= $all[rand(0, strlen($all) - 1)];
        }

        // xáo trộn chuỗi để random hơn
        return str_shuffle($password);
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
    public function show(Request $request, string $id)
    {
        $user = User::findOrFail($id);
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

        $request->validate([
            'fullname' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $id,
            'address' => 'nullable|string|max:255',
        ], [
            'phone.unique' => 'Số điện thoại đã được sử dụng.',
        ]);

        try {
            $user->fullname = $request->input('fullname');
            $user->phone = $request->input('phone');
            $user->address = $request->input('address');
            $user->save();

            return response()->json([
                'message' => 'Thông tin đã được cập nhật thành công.',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Đã xảy ra lỗi server khi cập nhật thông tin.'], 500);
        }
    }


    protected  function uploadAvatar(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = $file->hashName();
            $path = $file->storePubliclyAs('avatar', $filename, 'public');
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $path;
        }
        $user->save();

        return response()->json([
            'message' => 'Thông tin và ảnh đại diện đã được cập nhật thành công.',
            'user' => $user,
            'avatar_url' => $user->avatar ? Storage::url($user->avatar) : null
        ]);
    }


    public function updateStatus(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'mess' => "User not found"
            ], 404);
        }

        $user->status = $request->status;

        $user->save();

        return response()->json([
            'mess' => 'Complete',
            'user' => $user
        ]);
    }

    public function insertStaff(Request $request)
    {
        try {
            $data = $request->validate(
                [
                    'phone' => 'required|digits:10|unique:users,phone',
                    'fullname' => 'required|string|max:255',
                    'username' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'password' => 'required|string|min:6',
                ],
                [
                    'phone.required' => 'Vui lòng nhập số điện thoại',
                    'phone.digits' => 'Số điện thoại phải đủ 10 chữ số',
                    'phone.unique' => 'Số điện thoại đã tồn tại',

                    'fullname.required' => 'Vui lòng nhập họ và tên',

                    'username.required' => 'Vui lòng nhập tên đăng nhập',
                    'username.unique' => 'Tên đăng nhập đã tồn tại',

                    'email.required' => 'Vui lòng nhập email',
                    'email.email' => 'Email không đúng định dạng',
                    'email.unique' => 'Email đã tồn tại',

                    'password.required' => 'Vui lòng nhập mật khẩu',
                    'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                    'password.confirmed' => 'Xác nhận mật khẩu không khớp',
                ]
            );


            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => bcrypt($data['password']),
                'fullname' => $data['fullname'],
            ]);

            $user->assignRole('quanly');

            $token = $user->createToken('auth')->plainTextToken;

            return response()->json([
                'message' => 'Thêm người dùng thành công',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'fullname' => $user->fullname,
                    'password' => $user->password,
                    'role' => $user->getRoleNames()->first()
                ],
                'token' => $token
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi: ' . $th->getMessage()
            ], 500);
        }
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'shipper_id' => 'required|exists:users,id',
        ]);

        $shipper = User::find($request->shipper_id);
        $shipper->last_position_lat = $request->lat;
        $shipper->last_position_lng = $request->lng;
        $shipper->save();

        return response()->json(['success' => true]);
    }

    public function getLastLocation($id)
    {
        $shipper = User::find($id);

        if (!$shipper) {
            return response()->json([
                'success' => false,
                'message' => 'Shipper không tồn tại'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'lat' => $shipper->last_position_lat,
            'lng' => $shipper->last_position_lng,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function assignSingleRole(Request $request)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::find($request->user_id);
        $user->syncRoles([$request->role]);

        return response()->json(['message' => 'Vai trò đã được cập nhật.']);
    }
}
