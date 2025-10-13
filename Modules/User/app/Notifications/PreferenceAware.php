<?php

namespace Modules\User\Notifications;

use Modules\User\Services\NotificationPreferenceService;

trait PreferenceAware
{
        /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return (new NotificationPreferenceService())
            ->allowedChannels($notifiable , $this->type , $this->forcedChannels);
    }

}
