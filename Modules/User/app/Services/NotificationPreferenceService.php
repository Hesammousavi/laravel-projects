<?php

namespace Modules\User\Services;

use Modules\User\Models\User;

class NotificationPreferenceService
{
    public function getPerferences(User $user)
    {
        return cache()->remember("user:preferences:{$user->id}" , 60 , function() use ($user) {
            $defaultsPreferences = config('user.notifications.defaults');
            $userPreferences = $user->notificationPreferences?->preferences ?? [];

            return collect($defaultsPreferences)->map(
                function($channels , $type) use ($userPreferences) {
                    return collect($channels)->merge($userPreferences[$type] ?? [])->toArray();
                }
            );
        });

    }

    public function update(User $user, string $type , string $channel , bool $value)
    {
        $userPreferences = $user->notificationPreferences()->firstOrCreate();
        $defaultsPreferences = config('user.notifications.defaults');
        $defaultPreferenceValue = $defaultsPreferences[$type][$channel] ?? null;

        $preferences = collect($userPreferences->preferences ?? []);
        $channels = collect($preferences->get($type , []));

        if($defaultPreferenceValue === $value) {
            $channels->forget($channel);

            if($channels->isEmpty()) {
                $preferences->forget($type);
            } else {
                $preferences->put($type, $channels->toArray());
            }
        } else {
            $channels->put($channel , $value);
            $preferences->put($type, $channels->toArray());
        }

        $userPreferences->update(['preferences' => $preferences->toArray() ]);
    }

    public function allowedChannels(User $user , string $type , ?array $forcedChannels = []) : array
    {
        $preferences = $this->getPerferences($user);
        $channels = config('user.notifications.channels');

        return collect($channels)
            ->filter(fn($_ , $key) =>  $preferences[$type][$key] ?? false)
            ->values()
            ->merge($forcedChannels)
            ->toArray();
    }
}
