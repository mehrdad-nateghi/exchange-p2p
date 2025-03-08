<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IranianNationalCode implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $value = strval($value);

        if (!preg_match('/^\d{8,10}$/', $value) || preg_match('/^(.)\1{9}$/', $value)) {
            return false;
        }

        $value = str_pad($value, 10, '0', STR_PAD_LEFT);

        $sub = 0;
        for ($i = 0; $i < 9; $i++) {
            $sub += $value[$i] * (10 - $i);
        }

        $control = $sub % 11;
        $controlDigit = $control < 2 ? $control : 11 - $control;

        return (int) $value[9] === $controlDigit;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.iranian_national_code_is_invalid');
    }
} 