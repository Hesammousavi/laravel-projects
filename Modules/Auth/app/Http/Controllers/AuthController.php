<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        return 'register is done';
        // $user = User::create($request->validated());

        // return response()->json('user created successfully');
    }
}
