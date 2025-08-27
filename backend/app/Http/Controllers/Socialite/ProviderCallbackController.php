<?php

namespace App\Http\Controllers\Socialite;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
class ProviderCallbackController extends Controller
{
    public function __invoke(Request $request, string $provider)
    {
        if (!in_array($provider, ['github', 'google'], true)) {
            return response()->json(['message' => 'Invalid provider'], 422);
        }
    
        $isNew = false; // <- cờ đánh dấu user mới
    
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $existingUser = User::where('email', $socialUser->getEmail())->first();
    
            if ($existingUser) {
                if (!$existingUser->provider_id || !$existingUser->provider_name) {
                    return response()->json([
                        'message' => 'Email này đã đăng ký. Vui lòng đăng nhập bằng mật khẩu',
                    ], 409);
                }
                $user = $existingUser;
            } else {
                DB::transaction(function () use ($provider, $socialUser, &$user, &$isNew) {
                    $issuedAt = now();
                    $expiryAt = now()->addDays(7);
    
                    $user = User::create([
                        'name'                   => $socialUser->getName() ?? $socialUser->getNickname(),
                        'email'                  => $socialUser->getEmail(),
                        'provider_id'            => $socialUser->getId(),
                        'provider_name'          => $provider,
                        'provider_token'         => $socialUser->token ?? null,
                        'provider_refresh_token' => $socialUser->refreshToken ?? null,
                        'username'               => $socialUser->getName() ?? $socialUser->getEmail(),
                        'avatar'                 => null,
                    ]);
    
                    if (method_exists($user, 'assignRole')) {
                        $user->assignRole('khachhang');
                    }
    
                    // attach discount cho user mới
                    $newUserDiscounts = Discount::query()
                        ->where('user_level', 'new')
                        ->where('status', 'active')
                        ->where('source', 'for_users')
                        ->get();
    
                    if ($newUserDiscounts->isNotEmpty()) {
                        $data = [];
                        foreach ($newUserDiscounts as $discount) {
                            $data[$discount->id] = [
                                'point_used'   => 0,
                                'exchanged_at' => $issuedAt,
                                'expiry_at'    => $expiryAt,
                                'used_at'      => null,
                                'source'       => 'register_reward',
                                'created_at'   => $issuedAt,
                                'updated_at'   => $issuedAt,
                            ];
                        }
                        // tránh gắn trùng nếu có retry
                        $user->discounts()->syncWithoutDetaching($data);
                    }
    
                    $isNew = true; // <- chỉ user mới tạo
                });
            }
    
            if (!$user->avatar || Str::contains((string) $user->avatar, 'googleusercontent.com')) {
                $user->avatar = $socialUser->getAvatar();
                $user->save();
            }
    
            $token = $user->createToken('api_token')->plainTextToken;
    
            // GỬI MAIL: chỉ user mới, không chặn flow nếu lỗi
            if ($isNew) {
                try {
                    // Ưu tiên queue để không chặn response (setup QUEUE_CONNECTION)
                    // Mail::to($user->email)->queue(new \App\Mail\WelcomeMail($user));
    
                    // Nếu chưa dùng queue thì tạm send, nhưng bọc try/catch:
                    Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));
    
                    // (tuỳ chọn) đánh dấu đã gửi để tránh trùng:
                    // $user->forceFill(['welcome_emailed_at' => now()])->save();
                } catch (\Throwable $mailEx) {
                    Log::warning('Send welcome mail failed', [
                        'user_id' => $user->id,
                        'err'     => $mailEx->getMessage(),
                    ]);
                }
            }
    
            return response()->json([
                'token' => $token,
                'user'  => [
                    'id'       => $user->id,
                    'username' => $user->username,
                    'email'    => $user->email,
                    'avatar'   => $user->avatar,
                    'role'     => $user->getRoleNames()->first(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
}
