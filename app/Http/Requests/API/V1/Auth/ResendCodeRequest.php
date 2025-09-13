<?php

namespace App\Http\Requests\API\V1\Auth;

use App\Enums\VerificationCodeTypeEnum;
use App\Enums\VerificationCodeViaEnum;
use App\Rules\CanSendCodeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResendCodeRequest extends FormRequest
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
                'exists:verification_codes,to',
                new CanSendCodeRule($via, $type), // Use the default cooldown from config
                Rule::when(
                    $via === VerificationCodeViaEnum::EMAIL->value,
                    fn() => [
                        'email:filter',
                    ]
                ),
                Rule::when(
                    $via === VerificationCodeViaEnum::MOBILE->value,
                    fn() => [
                        'ir_mobile:zero',
                    ]
                ),
            ],
            'via' => ['required', Rule::in([
                VerificationCodeViaEnum::EMAIL->value,
                VerificationCodeViaEnum::MOBILE->value
            ])],
            'type' => ['required',Rule::enum(VerificationCodeTypeEnum::class)],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $via = $this->request->getInt('via');

        if ($via === VerificationCodeViaEnum::EMAIL->value) {
            return [
                'to' => trans('validation.attributes.email'),
            ];
        } else {
            return [
                'to' => trans('validation.attributes.mobile'),
            ];
        }
    }
}
