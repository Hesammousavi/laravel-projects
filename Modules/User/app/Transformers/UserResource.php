<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ,
            'email' => $this->email,
            'phone' => $this->phone,
            'telegram_bot_register_command' => $this->makeTelegramBotRegisterCommand()
        ];
    }
}
