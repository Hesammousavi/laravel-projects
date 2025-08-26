<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Auth\Enums\ContactType;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Http\Requests\SendVerificationRequest;
use Modules\Auth\Http\Requests\VerifyVerificationRequest;
use Modules\Auth\Services\VerificationCodeService;
use Modules\Base\Http\Controllers\ApiController;

class VerificationController extends ApiController {

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

        if(! $this->sendCodeByContactType($request , $code)) {
            return $this->errorResponse(null , [
                'contact' => [
                    __('auth::verification.code_sending_failed')
                ],
            ] , code : 422);
        }

        return $this->successResponse(__('auth::verification.code_sent_successfully'));
    }

    public function verifyCode(VerifyVerificationRequest $request)
    {
        if($request->action === VerificationActionType::VERIFY) {
            return $this->makeVerifyUserContact($request);
        }

        if($request->action === VerificationActionType::CHANGE_INFO) {
            return $this->makeChangeInfoContact($request);
        }

        // next step is to verify the code
        $token = $this->verificationCodeService->createVerificationToken($request);

        return $this->successResponse(null , [
            'token' => $token,
        ]);
    }


    private function sendCodeByContactType(SendVerificationRequest $request ,$code) : bool
    {
        return match ($request->contactType) {
            ContactType::EMAIL => $this->verificationCodeService->sendCodeAsMail($request , $request->input('contact') , $code),
            ContactType::PHONE => $this->verificationCodeService->sendCodeAsSms($request , $request->input('contact') , $code)
        };
    }

    private function makeVerifyUserContact(VerifyVerificationRequest $request)
    {
        $user = Auth::user();
        $user->verifiedContact($request->contactType);
        return $this->successResponse(__('auth::verification.contact_verified_successfully'));
    }

    private function makeChangeInfoContact(VerifyVerificationRequest $request)
    {
        $user = Auth::user();
        $user->changeContact($request->contactType , $request->input('contact'));
        return $this->successResponse(__('auth::verification.contact_changed_successfully'));
    }
}
