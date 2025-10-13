<?php

use Modules\Base\Notification\Channels\SmsChannel;
use Modules\TelegramBot\Notifications\Channels\TelegramChannel;

return [
    'name' => 'User',


    'notifications' => [
        'defaults' => [
            'welcome_message' => [
                'email' => false,
                'sms' => true,
                'telegram' => true
            ],
        ],

        'channels' => [
            'email' => 'mail',
            'sms' => SmsChannel::class,
            'telegram' => TelegramChannel::class
        ]
    ]
];
