<?php

namespace App\Services\SMS\Services\Farapayamak;

use App\Services\SMS\Interface\SMSProviderInterface;
use Illuminate\Support\Facades\Log;

class FarapayamakProvider implements SMSProviderInterface
{
    public function send(string $to, string $message): mixed
    {
        try {
            Log::info('Send from FarapayamakProvider',[
                'to' => $to,
                'message' => $message,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendByBaseNumber(string $to, string $message): bool
    {
        return $this->send($to, $message);
    }
}
