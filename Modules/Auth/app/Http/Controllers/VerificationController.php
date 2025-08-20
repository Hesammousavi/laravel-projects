<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Auth\Enums\ContactType;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Http\Requests\SendVerificationRequest;
use Modules\Auth\Http\Requests\VerifyVerificationRequest;
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
            $request->action,
            $request->contactType,
        );

        $responseStatus = false;

        if($request->contactType === ContactType::EMAIL) {
            // send email
            $responseStatus = $this->verificationCodeService->sendCodeAsMail($request , $request->input('contact') , $code);
        }

        if($request->contactType === ContactType::PHONE) {
            $responseStatus = $this->verificationCodeService->sendCodeAsSms($request , $request->input('contact') , $code);
        }

        if($responseStatus) {
            return response()->json(__('auth::verification.code_sent_successfully'));
        }

        return response()->json([
            'errors' => [
                'contact' => [
                    __('auth::verification.code_sending_failed')
                ],
            ],
        ], 422);
    }

    public function verifyCode(VerifyVerificationRequest $request)
    {
        // next step is to verify the code
        $token = $this->verificationCodeService->createVerificationToken($request);

        return response()->json([
            'token' => $token,
        ]);
    }
}
