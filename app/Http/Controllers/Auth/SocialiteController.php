<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SocialiteController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            $user = User::updateOrCreate([
                'email' => $socialUser->getEmail(),
            ], [
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'google_id' => $provider === 'google' ? $socialUser->getId() : null,
            ]);
            
            Auth::login($user, true);
            
            return redirect()->intended(route('home'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Socialite Error: ' . $e->getMessage(), [
                'provider' => $provider,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')->withErrors(['email' => 'Gagal login menggunakan ' . ucfirst($provider) . ': ' . $e->getMessage()]);
        }
    }
}
