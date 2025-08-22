<?php

namespace Modules\Auth\Actions;

use Illuminate\Support\Facades\Auth;
use Modules\Auth\Http\Requests\ForgotPasswordRequest;

class ForgotPassword
{
    public function handle(ForgotPasswordRequest $request)
    {
        if(!$request->user()) {
            throw new \Exception('User not found');
        }

        $request->user()->update([
            'password' => bcrypt($request->input('password')),
        ]);

        return $request->user();
    }
}
