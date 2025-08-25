<?php

namespace Modules\Base\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private const MSGWAY_API_URL = 'https://api.msgway.com/send';
    private const SMS_METHOD = 'sms';

    /**
     * Send SMS message using MsgWay API
     */
    public function send(string $mobile, int $templateId, array $params = []): bool
    {
        try {
            $response = Http::withHeaders([
                'apiKey' => config('auth.msgway.api_key'),
            ])->post(self::MSGWAY_API_URL, [
                'mobile' => $mobile,
                'method' => self::SMS_METHOD,
                'templateID' => $templateId,
                'params' => $params,
            ]);

            $response->throw();

            return $response->successful();
        } catch (\Throwable $th) {
            Log::error('SMS sending failed', [
                'mobile' => $mobile,
                'template_id' => $templateId,
                'error' => $th->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Send verification code via SMS
     */
    public function sendVerificationCode(string $mobile, int $code): bool
    {
        return $this->send($mobile, 3, [(string) $code]);
    }
}
