<?php

namespace App\Notifications;

use App\Services\SMS\Interface\SMSProviderInterface;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SMSChannel
{
    protected $smsProvider;

    public function __construct(SMSProviderInterface $smsProvider)
    {
        $this->smsProvider = $smsProvider;
    }

    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toSMS')) {
            throw new \Exception('toSMS method not found in notification class');
        }

        $data = $notification->toSMS($notifiable);

        if (!is_array($data)) {
            throw new \Exception('toSMS method must return an array');
        }

        $this->smsProvider->send($data['to'], $data['message']);
    }
}
