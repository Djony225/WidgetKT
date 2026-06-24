<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use App\Models\OauthAccount;

class GoogleAuthController extends Controller
{
    //
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callbackFromGoogle(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        if(!$user){
            $user = User::create([
                'name' =>$googleUser->name,
                'email' => $googleUser->email,
                'password' => bcrypt(str()->random(16)),
            ]);
        }

        OauthAccount::updateOrCreate([
            'user_id' => $user->id,
            'provider' => 'google',
        ],
        [
            'provider_user_id' => $googleUser->getId(),
            'token' => $googleUser->token,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $frontendUrl = "http://localhost:5173/dashbord";

        return redirect()->away($frontendUrl . "?token=" . $token);
    }


}
