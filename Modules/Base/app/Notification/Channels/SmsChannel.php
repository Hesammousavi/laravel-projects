<?php

namespace Modules\Base\Notification\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class SmsChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if( ! method_exists($notification , 'toSms') ) {
            throw new InvalidArgumentException(
                    'Notification [' . get_class($notification) . '] does not have a toSms method.'
            );
        }

        $data = $notification->toSms($notifiable);


        foreach (['phone_number' , 'message'] as $field) {
            if(empty($data[$field])) {
                throw new InvalidArgumentException("Missing required field: {$field}");
            }
        }

        $connection = $data['connection'] ?? config('base.sms.default');

        switch ($connection) {
            case 'kavenegar':
                $this->sendSmsByKavenegar($data);
                break;

            case 'farazsms':
                $this->snedSmsByFarazSMS($data);

                break;

            default:
                throw new InvalidArgumentException("connection is not suppported, [kavenegar , ...] ");
        }


    }

    protected function sendSmsByKavenegar($data){
        $apiKey = config('base.sms.connections.kavenegar.api_key');
        $baseUrl = config('base.sms.connections.kavenegar.base_url');

        Http::get("{$baseUrl}{$apiKey}/sms/send.json", [
            'receptor' => $data['phone_number'],
            'message' => $data['message'],
            'sender' => config('base.sms.connections.kavenegar.sender')
        ]);
    }

    protected function snedSmsByFarazSMS($data)
    {
        // code
    }
}
