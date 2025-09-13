<?php

namespace App\Services\Localization;

class LocalizationService
{
    public static function message(string $key, array $props = [], ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $message = trans($key, [], $locale);

        foreach ($props as $propKey => $propValue) {
            $message = str_replace(":{$propKey}", $propValue, $message);
        }

        return $message;
    }
}
