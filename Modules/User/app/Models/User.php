<?php

namespace Modules\User\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Auth\Enums\ContactType;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'telegram_chat_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function verifiedContact(ContactType $contactType)
    {
        $dataVerification = [];

        if($contactType === ContactType::EMAIL && is_null($this->email_verified_at)) {
           $dataVerification['email_verified_at'] = now();
        }

        if($contactType === ContactType::PHONE && is_null($this->phone_verified_at)) {
            $dataVerification['phone_verified_at'] = now();
        }

        $this->forceFill($dataVerification)->save();
    }

    public function changeContact(ContactType $contactType , string $contact)
    {
        $this->forceFill([
            $contactType->value => $contact,
            $contactType->value . '_verified_at' => now(),
        ])->save();
    }

    public function unverifiedContacts()
    {
        return collect([
            ContactType::EMAIL->value => $this->email_verified_at,
            ContactType::PHONE->value => $this->phone_verified_at,
        ])->filter(fn($value) => is_null($value))->keys();
    }

    public function identifierFromRequest()
    {
        return hash('sha256' , request()->userAgent() . ':' .request()->ip() . ':' . $this->id);
    }

    public function telegramChat(): HasOne
    {
        return $this->hasOne(TelegraphChat::class , 'id' , 'telegram_chat_id');
    }


    public function makeTelegramBotRegisterCommand() : ?string
    {
        if($this->telegram_chat_id) {
            return null;
        }

        $data = [
            'user_id' => $this->id,
            'timestamp' => now()
        ];

        $token = encrypt($data);

        return "/login {$token}";
    }


    public function notificationPreferences()
    {
        return $this->hasOne(UserNotificationPreference::class);
    }
}
