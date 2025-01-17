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
            $titleTemplate = trans("{$this->baseKey}.{$key}.title", [], $locale);
            $messageTemplate = trans("{$this->baseKey}.{$key}.message", [], $locale);

            if ($titleTemplate !== "{$this->baseKey}.{$key}.title" &&
                $messageTemplate !== "{$this->baseKey}.{$key}.message") {

                $this->validateAttributesForTemplate($messageAttributes, $titleTemplate, $messageTemplate);
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
        $titles = [];
        $messages = [];
        $sanitizedMessageAttributes = $this->sanitizeAttributes($messageAttributes);

        foreach ($this->supportedLocales as $locale) {
            $titleKey = "{$this->baseKey}.{$key}.title";
            $messageKey = "{$this->baseKey}.{$key}.message";

            $titleTemplate = trans($titleKey, [], $locale);
            $messageTemplate = trans($messageKey, [], $locale);

            if ($titleTemplate === $titleKey || $messageTemplate === $messageKey) {
                continue;
            }

            $title = trans($titleKey, $sanitizedMessageAttributes, $locale);
            $message = trans($messageKey, $sanitizedMessageAttributes, $locale);

            $titles[$locale] = is_array($title) ? implode($this->separator, $title) : $title;
            $messages[$locale] = is_array($message) ? implode($this->separator, $message) : $message;
        }

        if (empty($messages) || empty($titles)) {
            throw new InvalidArgumentException("No valid translations found for key: {$key}");
        }

        return [
            'title' => $titles,
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
            $titleKey = "{$this->baseKey}.{$key}.title";
            $messageKey = "{$this->baseKey}.{$key}.message";

            // Force specific locale, don't allow fallback
            $titleTranslation = Lang::get($titleKey, [], $locale, false);
            $messageTranslation = Lang::get($messageKey, [], $locale, false);

            // Check if translations exist
            if ($titleTranslation === $titleKey || $messageTranslation === $messageKey) {
                $invalidLocales[] = $locale;
            }
        }

        if (!empty($invalidLocales)) {
            throw new InvalidArgumentException(
                "Translation key '{$key}' is missing or invalid in these locales: " . implode(', ', $invalidLocales)
            );
        }
    }

    private function validateAttributesForTemplate(array $attributes, $titleTemplate, $messageTemplate): void
    {
        // Get placeholders from both title and message
        $titlePlaceholders = $this->extractPlaceholders($titleTemplate);
        $messagePlaceholders = $this->extractPlaceholders($messageTemplate);
        $allPlaceholders = array_unique(array_merge($titlePlaceholders, $messagePlaceholders));

        // If there are no placeholders, but attributes were provided
        if (empty($allPlaceholders) && !empty($attributes)) {
            throw new InvalidArgumentException("No placeholders in templates, but attributes were provided");
        }

        // If there are placeholders, validate the attributes
        if (!empty($allPlaceholders)) {
            // Check for required placeholders
            foreach ($allPlaceholders as $placeholder) {
                if (!array_key_exists($placeholder, $attributes)) {
                    throw new InvalidArgumentException("Missing required attribute: {$placeholder}");
                }
            }

            // Check for extra attributes
            foreach (array_keys($attributes) as $key) {
                if (!in_array($key, $allPlaceholders)) {
                    throw new InvalidArgumentException("Unexpected attribute: {$key}");
                }
            }
        }
    }

    private function extractPlaceholders($template): array
    {
        $placeholders = [];

        if (is_array($template)) {
            foreach ($template as $line) {
                if (is_string($line)) {
                    $placeholders = array_merge($placeholders, $this->findPlaceholders($line));
                }
            }
        } elseif (is_string($template)) {
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
