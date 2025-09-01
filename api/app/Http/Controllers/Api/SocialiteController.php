<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class SocialiteController extends Controller
{
    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback(string $provider)
    {
        $socialiteUser = Socialite::driver($provider)->stateless()->user();

        $user = User::where('auth_provider_id', $socialiteUser->getId())
            ->where('auth_provider', $provider)
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
                'auth_provider' => $provider,
                'auth_provider_id' => $socialiteUser->getId(),
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user);

        return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/dashboard-social');
    }
}
