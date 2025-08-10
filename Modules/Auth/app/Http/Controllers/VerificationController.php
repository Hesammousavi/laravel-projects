<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Http\Requests\SendVerificationRequest;
use Modules\Auth\Services\VerificationCodeService;

class VerificationController extends Controller {

    public function __construct(private VerificationCodeService $verificationCodeService)
    {
    }

    public function sendCode(SendVerificationRequest $request)
    {
        // generate code
        $code = $this->verificationCodeService->generateCode(
            $request->input('contact'),
            VerificationActionType::tryFrom($request->input('action')),
            $request->contactType,
        );


        dd($code);
        // as contanct ( send sms or email)
        // send response to user
    }
}
