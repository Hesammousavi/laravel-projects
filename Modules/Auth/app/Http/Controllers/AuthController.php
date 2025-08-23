<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Modules\Auth\Actions\CreateUserToken;
use Modules\Auth\Actions\ForgotPassword;
use Modules\Auth\Actions\RegisterUser;
use Modules\Auth\Http\Requests\ForgotPasswordRequest;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\Base\Http\Controllers\ApiController;
use Modules\User\Models\User;

class AuthController extends ApiController
{
    public function checkUser(Request $request)
    {
        $cotanct = $request->input('contact');

        $user = User::where('email', $cotanct)
                ->orWhere('phone', $cotanct)
                ->exists();

        if($user) {
            return $this->successResponse(__('auth::auth.user_exists'));
        }

        return $this->errorResponse(__('auth::auth.user_not_found'), code: 404);
    }

    public function login(LoginRequest $request)
    {
        /** @var User */
        $user = Auth::user();
        $token = (new CreateUserToken)->handle($user, isEncrypted: true);

        return $this->successResponse(__('auth::auth.login_success'),
            data:[
                'token' => $token,
            ],
            cookies: [
                cookie(
                    'x_web_token' ,
                    $token,
                    60 * 24 * 30, // 30 days
                    '/',
                    config('session.domain'),
                    true,
                    true,
                )
            ]
        );
    }

    public function register(RegisterRequest $request)
    {
        $user = (new RegisterUser)->handle($request);
        $token = (new CreateUserToken)->handle($user, isEncrypted: true);

        return $this->successResponse(__('auth::auth.registration_success'),
            data: [
                'token' => $token,
            ],
            cookies: [
                cookie(
                    'x_web_token' ,
                    $token,
                    60 * 24 * 30, // 30 days
                    '/',
                    config('session.domain'),
                    true,
                    true,
                )
            ]
        );

    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            (new ForgotPassword)->handle($request);
        } catch (\Throwable $th) {
            return $this->errorResponse(__('auth::auth.password_reset_failed'), code: 500);
        }

        return $this->successResponse(__('auth::auth.password_reset_success'));
    }
}
