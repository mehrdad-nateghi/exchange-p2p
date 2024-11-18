<?php

namespace App\Http\Requests\API\V1\Auth;

use App\Enums\VerificationCodeTypeEnum;
use App\Enums\VerificationCodeViaEnum;
use App\Rules\VerificationCodeNotExpiredRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use TimeHunter\LaravelGoogleReCaptchaV3\Validations\GoogleReCaptchaV3ValidationRule;

class SendCodeRequest extends FormRequest
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

        $via = $this->request->getInt('via');
        $type = $this->request->getInt('type');

        return [
            'to' => [
                'bail',
                'required',
                Rule::when(
                    $via === VerificationCodeViaEnum::EMAIL->value,
                    fn() => [
                        'email:filter',
                        new VerificationCodeNotExpiredRule($via, $type),
                    ]
                ),
                Rule::when(
                    $type === VerificationCodeTypeEnum::SET_PASSWORD->value,
                    fn() => [
                        'unique:users,email',
                    ]
                ),
            ],
            'via' => ['required', Rule::in([VerificationCodeViaEnum::EMAIL->value])],
            'type' => ['required',Rule::enum(VerificationCodeTypeEnum::class)],
            'g-recaptcha-response' => ['required',new GoogleReCaptchaV3ValidationRule('send-code')],
        ];
    }
}