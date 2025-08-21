<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Modules\Auth\Actions\RegisterUser;
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
            return response()->json('user exsits');
        }

        return response()->json('user not found' , 404);
    }

    public function register(RegisterRequest $request)
    {
        $user = (new RegisterUser)->handle($request);

        $token = $user->createToken('x_web_token' , expiresAt: now()->addDays(30))->plainTextToken;

        return response()->json([
            'token' => Crypt::encryptString($token),
        ]);
    }
}
