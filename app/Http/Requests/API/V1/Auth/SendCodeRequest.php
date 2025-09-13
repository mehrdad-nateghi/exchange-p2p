<?php

namespace App\Http\Requests\API\V1\Auth;

use App\Enums\VerificationCodeTypeEnum;
use App\Enums\VerificationCodeViaEnum;
use App\Rules\CanSendCodeRule;
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
            'to' => $this->getToRules($via, $type),
            'type' => $this->getTypeRules($via),
            'via' => [
                'required',
                Rule::in([
                    VerificationCodeViaEnum::EMAIL->value,
                    VerificationCodeViaEnum::MOBILE->value
                ])
            ],
            'g-recaptcha-response' => ['required', new GoogleReCaptchaV3ValidationRule('send-code')],
        ];
    }

    private function getTypeRules(int $via): array
    {
        $typeMap = [
            VerificationCodeViaEnum::EMAIL->value => VerificationCodeTypeEnum::VERIFICATION_EMAIL->value,
            VerificationCodeViaEnum::MOBILE->value => VerificationCodeTypeEnum::VERIFICATION_MOBILE->value,
        ];

        return [
            'required',
            Rule::in([$typeMap[$via] ?? null])
        ];
    }

    private function getToRules(int $via, int $type): array
    {
        $rules = [
            'bail',
            'required',
            new CanSendCodeRule($via, $type), // Use the default cooldown from config
        ];

        if ($via === VerificationCodeViaEnum::EMAIL->value) {
            $rules[] = 'email:filter';
            $rules[] = 'unique:users,email';
        } elseif ($via === VerificationCodeViaEnum::MOBILE->value) {
            $rules[] = 'ir_mobile:zero';
            $rules[] = 'unique:users,mobile';
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $via = $this->request->getInt('via');

        return [
            'to' => $via === VerificationCodeViaEnum::EMAIL->value
                ? trans('validation.attributes.email')
                : trans('validation.attributes.mobile'),
        ];
    }
}
