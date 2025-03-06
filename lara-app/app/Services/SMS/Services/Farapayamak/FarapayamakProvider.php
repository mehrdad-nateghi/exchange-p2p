<?php

namespace App\Services\SMS\Services\Farapayamak;

use App\Services\SMS\Interface\SMSProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Farapayamak SMS Provider
 *
 * This class handles sending SMS messages via the Farapayamak API.
 * @see https://github.com/Farapayamak/PHP
 */
class FarapayamakProvider implements SMSProviderInterface
{
    protected string $endpoint = 'https://rest.payamak-panel.com/api/SendSMS/SendSMS';

    /**
     * Get the API credentials
     *
     * Fetches credentials fresh each time to avoid serialization issues with queued jobs
     *
     * @return array The credentials array
     */
    protected function getCredentials(): array
    {
        return [
            'username' => config('services.sms.farapayamak.username', ''),
            'password' => config('services.sms.farapayamak.password', ''),
            'from' => config('services.sms.farapayamak.from', '')
        ];
    }

    /**
     * Send an SMS message
     *
     * @param string $to The recipient's phone number
     * @param string $message The message content
     * @return mixed Tracking ID on success, throws exception on failure
     * @throws \Exception If sending fails
     *
     * Response codes based on API documentation:
     * Value: The tracking ID if successful, "0" if failed
     * RetStatus codes:
     *   1: Request completed successfully
     *   2: Credit is insufficient
     *   3: Daily sending limit reached
     *   4: Sending volume limit reached
     *   5: Sender number and user are not matched
     *   6: System is under maintenance
     *   7: Text contains filtered words
     *   9: Sending through web service lines is not allowed
     *   10: User is inactive
     *   11: Not sent
     *   12: User account is incomplete
     *   14: Text contains links
     *   15: No line feed at the end of the message
     *   35: Invalid data
     */
    public function send(string $to, string $message): mixed
    {
        try {
            // Format the phone number
            $to = $this->formatPhoneNumber($to);

            // Process message for multi-line support
            // Farapayamak uses \n for line breaks in SMS
            //$message = str_replace(["\r\n", "\r"], "\n", $message);

            // Get fresh credentials to avoid serialization issues
            $credentials = $this->getCredentials();

            // Prepare the request payload
            $payload = [
                'username' => $credentials['username'],
                'password' => $credentials['password'],
                'from' => $credentials['from'],
                'to' => $to,
                'text' => $message
            ];

            // Log the request payload (with password masked for security)
            $logPayload = $payload;
            if (isset($logPayload['password']) && !empty($logPayload['password'])) {
                $logPayload['password'] = substr($logPayload['password'], 0, 1) . '***';
            }

            // Count lines in the message for debugging
            $lineCount = substr_count($message, "\n") + 1;

            Log::info('Farapayamak SMS Request', [
                'payload' => $logPayload,
                'password_length' => strlen($payload['password']),
                'message_lines' => $lineCount,
                'message_length' => strlen($message)
            ]);

            // Send the request
            $result = $this->sendRequest($payload);

            // Check if the SMS was sent successfully
            if (isset($result['RetStatus']) && $result['RetStatus'] == 1 &&
                isset($result['StrRetStatus']) && $result['StrRetStatus'] == 'Ok') {
                return $result['Value'] ?? true;
            }

            // Handle error
            $errorMessage = $this->getErrorMessage($result['RetStatus'] ?? 0);
            throw new \Exception("Failed to send SMS: $errorMessage");

        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage(), [
                'to' => $to,
                'exception' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send an SMS message using a base number
     *
     * @param string $to The recipient's phone number
     * @param string $message The message content
     * @return bool True on success, throws exception on failure
     * @throws \Exception If sending fails
     */
    public function sendByBaseNumber(string $to, string $message): bool
    {
        // implement
    }

    /**
     * Send HTTP request to Farapayamak API
     *
     * @param array $data Request data
     * @return array Response data
     * @throws \Exception If request fails
     */
    private function sendRequest(array $data): array
    {
        try {
            // Log request data with masked password
            $logData = $data;

            /*if (isset($logData['password'])) {
                $logData['password'] = substr($logData['password'], 0, 1) . '***';
            }*/

            Log::debug('Farapayamak request', [
                'url' => $this->endpoint,
                'data' => $logData
            ]);

            $response = Http::timeout(10)
                ->withoutVerifying()
                ->retry(3, 1000)
                ->post($this->endpoint, $data);

            Log::debug('Farapayamak response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->failed()) {
                throw new \Exception('HTTP request failed with status: ' . $response->status());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('API request error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Format phone number to the correct format for the API
     *
     * @param string $phoneNumber
     * @return string
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Make sure the phone number starts with the correct format
        if (substr($phoneNumber, 0, 2) === '98') {
            $phoneNumber = '0' . substr($phoneNumber, 2);
        } elseif (substr($phoneNumber, 0, 3) === '+98') {
            $phoneNumber = '0' . substr($phoneNumber, 3);
        } elseif (substr($phoneNumber, 0, 1) !== '0') {
            $phoneNumber = '0' . $phoneNumber;
        }

        return $phoneNumber;
    }

    /**
     * Get error message based on RetStatus code
     *
     * @param int $retStatus The RetStatus code from the API response
     * @return string The error message
     */
    private function getErrorMessage(int $retStatus): string
    {
        $errorMessages = [
            0 => 'Unknown error',
            1 => 'Request completed successfully',
            2 => 'Credit is insufficient',
            3 => 'Daily sending limit reached',
            4 => 'Sending volume limit reached',
            5 => 'Sender number and user are not matched',
            6 => 'System is under maintenance',
            7 => 'Text contains filtered words',
            9 => 'Sending through web service lines is not allowed',
            10 => 'User is inactive',
            11 => 'Not sent',
            12 => 'User account is incomplete',
            14 => 'Text contains links',
            15 => 'No line feed at the end of the message',
            35 => 'Invalid data'
        ];

        return $errorMessages[$retStatus] ?? "Unknown error code: $retStatus";
    }
}
