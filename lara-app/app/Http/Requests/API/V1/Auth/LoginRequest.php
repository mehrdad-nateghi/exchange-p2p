<?php

namespace App\Http\Requests\API\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use TimeHunter\LaravelGoogleReCaptchaV3\Validations\GoogleReCaptchaV3ValidationRule;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required','email:filter','exists:users,email'],
            'password' => ['required'],
            'g-recaptcha-response' => ['required',new GoogleReCaptchaV3ValidationRule('login')],
        ];
    }
}