<?php

namespace Modules\TelegramBot\Webhooks;

use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Facades\Log;
use Modules\User\Models\User;

class TelegramWebhook extends WebhookHandler
{
    public function start()
    {
        $this->chat->message('hello roocket!')->send();
    }

    public function login(string $token)
    {
        try {
            $data = decrypt($token);

            User::find($data['user_id'])->update([
                'telegram_chat_id' => $this->chat->id
            ]);

            $this->chat->message('you sing in successfully :)')->send();
        } catch (\Throwable $th) {
            $this->chat->message('اسکریپت وارد شده صحیح نیست،لطفا به درستی آن را وارد کنید')->send();
        }
    }
}
