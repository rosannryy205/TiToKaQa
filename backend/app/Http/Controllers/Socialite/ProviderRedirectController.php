<?php

namespace App\Http\Controllers\Socialite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class ProviderRedirectController extends Controller
{
    /**
    * Handle the incoming request.
    */
    public function __invoke(string $provider)
    {
        if (!in_array($provider, ['github', 'google'])) {
            return response()->json(['message' => 'Invalid provider'], 422);
        }

        try {
            return Socialite::driver($provider)->stateless()->redirect();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
