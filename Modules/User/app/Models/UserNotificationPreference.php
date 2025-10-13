<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\User\Database\Factories\UserNotificationPreferenceFactory;

class UserNotificationPreference extends Model
{
    use HasFactory;

    protected $table = 'users__notification_preferences';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id' , 'preferences'];


    public function casts()
    {
        return [
            'preferences' => 'array'
        ];
    }

    // protected static function newFactory(): UserNotificationPreferenceFactory
    // {
    //     // return UserNotificationPreferenceFactory::new();
    // }
}
