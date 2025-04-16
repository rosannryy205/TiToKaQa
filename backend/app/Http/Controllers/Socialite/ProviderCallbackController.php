<?php

namespace App\Http\Controllers\Socialite;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class ProviderCallbackController extends Controller
{
    public function __invoke(string $provider)
    {
        if (!in_array($provider, ['github', 'google'])) {
            return response()->json(['message' => 'Invalid provider'], 422);
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();

            $user = User::updateOrCreate([
                'provider_id' => $socialUser->getId(),
                'provider_name' => $provider,
            ], [
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'email' => $socialUser->getEmail(),
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken ?? null,
                'username' => $socialUser->name ?? $socialUser->email,
            ]);

            $token = $user->createToken('api_token')->plainTextToken;

            return redirect()->away("http://localhost:5173/google/callback?token=$token&user=$user");

            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }
}
