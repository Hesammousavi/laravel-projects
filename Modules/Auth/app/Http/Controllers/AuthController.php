<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Modules\Auth\Actions\ForgotPassword;
use Modules\Auth\Actions\RegisterUser;
use Modules\Auth\Http\Requests\ForgotPasswordRequest;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\User\Models\User;

class AuthController extends Controller
{
    public function checkUser(Request $request)
    {
        $cotanct = $request->input('contact');

        $user = User::where('email', $cotanct)
                ->orWhere('phone', $cotanct)
                ->exists();

        if($user) {
            return response()->json(__('auth::auth.user_exists'));
        }

        return response()->json(__('auth::auth.user_not_found'), 404);
    }

    public function login(LoginRequest $request)
    {
        $user = Auth::user();

        $token = $user->createToken('x_web_token' , expiresAt: now()->addDays(30))->plainTextToken;
        $encryptedToken = Crypt::encryptString($token);

        return response()->json([
            'message' => __('auth::auth.login_success'),
            'token' => $encryptedToken,
        ])->withCookie(cookie(
            'x_web_token' ,
            $encryptedToken,
            60 * 24 * 30, // 30 days
            '/',
            config('session.domain'),
            true,
            true,
        ));
    }

    public function register(RegisterRequest $request)
    {
        $user = (new RegisterUser)->handle($request);

        $token = $user->createToken('x_web_token' , expiresAt: now()->addDays(30))->plainTextToken;
        $encryptedToken = Crypt::encryptString($token);

        return response()->json([
            'message' => __('auth::auth.registration_success'),
            'token' => $encryptedToken,
        ])->withCookie(cookie(
            'x_web_token' ,
            $encryptedToken,
            60 * 24 * 30, // 30 days
            '/',
            config('session.domain'),
            true,
            true,
        ));
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $user = (new ForgotPassword)->handle($request);


        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('auth::auth.password_reset_failed'),
            ], 500);
        }

        return response()->json([
            'message' => __('auth::auth.password_reset_success'),
        ]);
    }
}
