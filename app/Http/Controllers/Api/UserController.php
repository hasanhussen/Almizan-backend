<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\FcmToken;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        // جلب الدور من قاعدة البيانات
        $role = Role::findByName($request->role);

        // تعيين الدور باستخدام spatie
        $user->assignRole($role);
        $user->email_verified_at = now();
        $user->save();

        return response()->json($user);
    }

    public function getProfile()
    {
        $user = Auth::user();
        return response()->json([
            'email' => $user->email,
            'name' => $user->name,
            'year' => $user->year,
            'studentId' => $user->id,
            'registered' => $user->created_at->format('d M Y'),
            'image' => $user->image
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $request->user()->currentAccessToken()->delete();
        FcmToken::where('token', $request->fcm_token)->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
