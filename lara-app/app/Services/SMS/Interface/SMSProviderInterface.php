<?php

namespace App\Services\SMS\Interface;


interface SMSProviderInterface
{
    public function send(string $to, string $message): mixed;
    public function sendByBaseNumber(string $to, string $message): bool;
}

