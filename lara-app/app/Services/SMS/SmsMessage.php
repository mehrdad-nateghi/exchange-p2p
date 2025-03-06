<?php

namespace App\Services\SMS;

use Illuminate\Support\Facades\Lang;
use InvalidArgumentException;

class SmsMessage
{
    private array $supportedLocales;
    private string $baseKey = 'sms';
    private string $separator = "\n"; // Use newline for SMS

    public function __construct(array $supportedLocales = ['en', 'fa'])
    {
        $this->supportedLocales = $supportedLocales;
    }

    /**
     * Get a formatted SMS message from the language files
     *
     * @param string $key The key in the sms language file
     * @param array $attributes The attributes to replace in the message
     * @param string|null $locale The locale to use (defaults to current locale)
     * @return string The formatted SMS message
     * @throws InvalidArgumentException If the key is not found
     */
    public function get(string $key, array $attributes = [], ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        
        // Sanitize attributes to prevent injection
        $sanitizedAttributes = $this->sanitizeAttributes($attributes);
        
        // Get the message template
        $messageKey = "{$this->baseKey}.{$key}";
        $messageTemplate = trans($messageKey, [], $locale);
        
        // Check if the message exists
        if ($messageTemplate === $messageKey) {
            // Try to find it in any supported locale as fallback
            foreach ($this->supportedLocales as $supportedLocale) {
                if ($supportedLocale === $locale) continue;
                
                $fallbackTemplate = trans($messageKey, [], $supportedLocale);
                if ($fallbackTemplate !== $messageKey) {
                    $messageTemplate = $fallbackTemplate;
                    break;
                }
            }
            
            // If still not found, throw an exception
            if ($messageTemplate === $messageKey) {
                throw new InvalidArgumentException("SMS message key not found: {$key}");
            }
        }
        
        // Replace the attributes in the message
        $message = trans($messageKey, $sanitizedAttributes, $locale);
        
        // Handle array messages (for multi-line messages)
        if (is_array($message)) {
            $message = implode($this->separator, $message);
        }
        
        return $message;
    }
    
    /**
     * Sanitize the attributes to prevent injection
     *
     * @param array $attributes The attributes to sanitize
     * @return array The sanitized attributes
     */
    private function sanitizeAttributes(array $attributes): array
    {
        return array_map(function ($value) {
            if (is_string($value)) {
                return strip_tags(trim($value));
            }
            return $value;
        }, $attributes);
    }
} 