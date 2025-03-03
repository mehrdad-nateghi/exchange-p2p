<?php

namespace App\Http\Requests\API\V1\Auth;

use App\Enums\LoginViaEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
        $via = $this->request->get('via');

        $rules = [
            'via' => ['required', Rule::in([
                LoginViaEnum::EMAIL->value,
                LoginViaEnum::MOBILE->value
            ])],
            'password' => ['required'],
            'g-recaptcha-response' => ['required', new GoogleReCaptchaV3ValidationRule('login')],
        ];

        if ($via == LoginViaEnum::EMAIL->value) {
            $rules['email'] = ['required', 'email:filter', 'exists:users,email'];
        } else if ($via == LoginViaEnum::MOBILE->value) {
            $rules['mobile'] = ['required', 'ir_mobile:zero', 'exists:users,mobile'];
        }

        return $rules;

        /*$type = $this->request->getInt('type');

        return [

            'email' => ['required','email:filter','exists:users,email'],
            'password' => ['required'],
            'g-recaptcha-response' => ['required',new GoogleReCaptchaV3ValidationRule('login')],
        ];*/
    }

    /**
     * Get the identifier for authentication based on login type
     *
     * @return array
     */
    public function getCredentials(): array
    {
        $via = LoginViaEnum::tryFrom($this->request->get('via'));

        $credentials = [
            'password' => $this->request->get('password')
        ];

        if ($via === LoginViaEnum::EMAIL) {
            $credentials['email'] = $this->request->get('email');
        } else if ($via === LoginViaEnum::MOBILE) {
            $credentials['mobile'] = $this->request->get('mobile');
        }

        return $credentials;
    }
}
