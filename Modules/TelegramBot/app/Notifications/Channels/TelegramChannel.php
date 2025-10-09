<?php

namespace Modules\TelegramBot\Notifications\Channels;

use DefStudio\Telegraph\Facades\Telegraph as TelegraphFacade;
use DefStudio\Telegraph\Telegraph;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class TelegramChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if( ! method_exists($notification , 'toTelegram') ) {
            throw new InvalidArgumentException(
                'Notification [' . get_class($notification) . '] does not have a toTelegram method.'
            );
        }

        if(is_null($notifiable->telegramChat)) {
            Log::error('there is no telegram chat relationship for the user');
            return;
        }

        $telegraphObject = TelegraphFacade::chat($notifiable->telegramChat);

        /**
         * @var Telegraph
         */
        $telegraph = $notification->toTelegram($notifiable , $telegraphObject);


        if(! $telegraph instanceof Telegraph) {
            throw new InvalidArgumentException(
                'toTelegram method inside [' . get_class($notification) . '] must return DefStudio\Telegraph\Telegraph.'
            );
        }


        try {
            $response = $telegraph->send();
            if( $response->failed() ) {
                $errorData = $response->json();
                dd($errorData);
                if(str($errorData['description'])->contains('blocked by the user')) {
                    $notifiable->telegramChat?->delete();
                    return;
                }

                // throw a new exection
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
