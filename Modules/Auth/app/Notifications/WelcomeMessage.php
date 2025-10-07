<?php

namespace Modules\Auth\Notifications;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Modules\Base\Notification\Channels\SmsChannel;
use Throwable;

class WelcomeMessage extends Notification
{
    use Queueable;

    public $tries = 4;

    public function backoff(): array
    {
        return [60 * 2 , 60 * 10 , 60 * 60];
    }

    /**
     * Create a new notification instance.
     */
    public function __construct() {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return [SmsChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to our application')
            ->greeting('Welcome to our application')
            ->line('Thank you for registering with us. We are glad to have you on board.')
            ->action('Dashboard', url('/dashboard'))
            ->line('Thank you for using our application!');
    }

    public function toSms($notifiable)
    {
        return [
            'phone_number' => '09111111100',
            'message' => 'سلام'
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [];
    }

    public function failed(Throwable $throwable) : void
    {
        Log::error("notificaiton failed : " . $throwable->getMessage());
    }
}
