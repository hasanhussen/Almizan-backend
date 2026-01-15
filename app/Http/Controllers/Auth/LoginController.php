<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use App\Models\FcmToken;
use App\Http\Resources\UserResource;

class LoginController extends Controller
{

    public function login(LoginRequest $request)
    {
        // البحث بين الكل، حتى المحذوفين ناعمًا
        if (!Auth::attempt($request->only('email', 'password')))
            return response()->json(
                [
                    'message' => 'invalid email or password'
                ],
                401
            );

        $userData = User::where('email', $request->email)->firstOrFail();
        if ($request->fcm_token != null) {
            FcmToken::firstOrCreate([
                'user_id' => $userData->id,
                'token'   => $request->fcm_token,
            ]);
        }
        // ✅ حساب فعّال — أنشئ التوكن وسجّل دخول
        $token = $userData->createToken('auth_Token')->plainTextToken;
        $userData->api_token = $token;

        if ($userData->email_verified_at == null) {
            event(new Registered($userData));
        }


        $user = new UserResource($userData);

        return response()->json(['user' => $user]);
    }
}
