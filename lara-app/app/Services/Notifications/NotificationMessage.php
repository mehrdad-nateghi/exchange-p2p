<?php

namespace App\Services\Notifications;

use Illuminate\Support\Facades\Lang;
use InvalidArgumentException;

class NotificationMessage
{
    private array $supportedLocales;
    private string $baseKey = 'database-notifications';
    private string $separator = PHP_EOL;

    public function __construct(array $supportedLocales = ['en', 'fa'])
    {
        $this->supportedLocales = $supportedLocales;
    }

    public function store(string $key, array $messageAttributes = [], array $info = []): array
    {
        $this->validateKey($key);
        $sanitizedMessageAttributes = $this->sanitizeAttributes($messageAttributes);

        foreach ($this->supportedLocales as $locale) {
            $template = trans("{$this->baseKey}.{$key}", [], $locale);
            if ($template !== "{$this->baseKey}.{$key}") {
                $this->validateAttributes($messageAttributes, $template);
                break;
            }
        }

        return [
            'key' => $key,
            'attributes' => $sanitizedMessageAttributes,
            'info' => $info
        ];
    }

    public function retrieve(string $key, array $messageAttributes = [], array $info = []): array
    {
        $messages = [];
        $sanitizedMessageAttributes = $this->sanitizeAttributes($messageAttributes);

        foreach ($this->supportedLocales as $locale) {
            $translationKey = "{$this->baseKey}.{$key}";
            $template = trans($translationKey, [], $locale);

            if ($template === $translationKey) {
                continue;
            }

            $message = trans($translationKey, $sanitizedMessageAttributes, $locale);
            $messages[$locale] = is_array($message) ? implode($this->separator, $message) : $message;
        }

        if (empty($messages)) {
            throw new InvalidArgumentException("No valid translations found for key: {$key}");
        }

        return [
            'message' => $messages,
            'attributes' => $sanitizedMessageAttributes,
            'info' => $info,
            'key' => $key
        ];
    }

    private function validateKey(string $key): void
    {
        $invalidLocales = [];

        foreach ($this->supportedLocales as $locale) {
            $translationKey = "{$this->baseKey}.{$key}";

            // Force specific locale, don't allow fallback
            $translation = Lang::get($translationKey, [], $locale, false);

            // Check if translation doesn't exist or isn't an array with required elements
            if ($translation === $translationKey || !is_array($translation) || !isset($translation[0]) || !isset($translation[1])) {
                $invalidLocales[] = $locale;
            }
        }

        if (!empty($invalidLocales)) {
            throw new InvalidArgumentException(
                "Translation key '{$key}' is missing or invalid in these locales: " . implode(', ', $invalidLocales)
            );
        }
    }

    private function validateAttributes(array $attributes, $template): void
    {
        // Extract placeholders from the template
        $placeholders = $this->extractPlaceholders($template);

        // Check for required placeholders
        foreach ($placeholders as $placeholder) {
            if (!array_key_exists($placeholder, $attributes)) {
                throw new InvalidArgumentException("Missing required attribute: {$placeholder}");
            }
        }

        // Check for extra attributes
        foreach (array_keys($attributes) as $key) {
            if (!in_array($key, $placeholders)) {
                throw new InvalidArgumentException("Unexpected attribute: {$key}");
            }
        }
    }

    private function extractPlaceholders($template): array
    {
        $placeholders = [];

        if (is_array($template)) {
            // If template is array, extract from all lines
            foreach ($template as $line) {
                if (is_string($line)) {
                    $placeholders = array_merge($placeholders, $this->findPlaceholders($line));
                }
            }
        } elseif (is_string($template)) {
            // If template is string, extract from single line
            $placeholders = $this->findPlaceholders($template);
        }

        return array_unique($placeholders);
    }

    private function findPlaceholders(string $text): array
    {
        preg_match_all('/:([a-zA-Z_][a-zA-Z0-9_]*)/', $text, $matches);
        return $matches[1] ?? [];
    }

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
