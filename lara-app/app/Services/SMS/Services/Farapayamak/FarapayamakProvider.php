<?php

namespace App\Services\SMS\Services\Farapayamak;

use App\Services\SMS\Interface\SMSProviderInterface;
use Illuminate\Support\Facades\Mail;

class FarapayamakProvider implements SMSProviderInterface
{
    public function send(string $to, string $messageText): mixed
    {
        try {
            Mail::raw($messageText, function ($mail) use ($to) {
                $mail->to($to)
                    ->subject('SMS Test Notification');
            });

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendByBaseNumber(string $to, string $message): bool
    {
        return $this->send($to, $message);
    }
}
