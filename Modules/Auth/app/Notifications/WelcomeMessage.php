<?php

namespace Modules\Auth\Notifications;

use DefStudio\Telegraph\Facades\Telegraph as TelegraphFacade;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Telegraph;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Base\Notification\Channels\SmsChannel;
use Modules\TelegramBot\Notifications\Channels\TelegramChannel;
use Modules\User\Notifications\PreferenceAware;
use Modules\User\Services\NotificationPreferenceService;
use Throwable;

class WelcomeMessage extends Notification implements ShouldQueue
{
    use Queueable, PreferenceAware;

    protected string $type = 'welcome_message';

    protected array $forcedChannels = [];

    /**
     * Create a new notification instance.
     */
    public function __construct() {}


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

    public function toTelegram($notifiable, Telegraph $telegraph)
    {
        return $telegraph->message('hello world')
            ->keyboard(Keyboard::make()->buttons([
                Button::make('Delete')->action('delete')->param('id', '42')
            ]));
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
