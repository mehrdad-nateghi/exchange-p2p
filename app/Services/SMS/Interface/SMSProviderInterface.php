<?php

namespace App\Services\SMS\Interface;

/**
 * SMS Provider Interface
 * 
 * Interface for SMS service providers
 */
interface SMSProviderInterface
{
    /**
     * Send an SMS message
     * 
     * @param string $to The recipient's phone number
     * @param string $message The message content
     * @return mixed True on success
     * @throws \Exception If sending fails after all retries
     */
    public function send(string $to, string $message): mixed;
    
    /**
     * Send an SMS message using a base number
     * 
     * @param string $to The recipient's phone number
     * @param string $message The message content
     * @return bool True on success
     * @throws \Exception If sending fails
     */
    public function sendByBaseNumber(string $to, string $message): bool;
}

