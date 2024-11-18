<?php

namespace App\Http\Requests\API\V1\Auth;

use App\Enums\VerificationCodeTypeEnum;
use App\Enums\VerificationCodeViaEnum;
use App\Rules\VerificationCodeNotExpiredRule;
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
                Rule::when(
                    $via === VerificationCodeViaEnum::EMAIL->value,
                    fn() => [
                        'email:filter',
                        new VerificationCodeNotExpiredRule($via, $type),
                    ]
                ),
            ],
            'via' => ['required', Rule::in([VerificationCodeViaEnum::EMAIL->value])],
            'type' => ['required',Rule::enum(VerificationCodeTypeEnum::class)],
        ];
    }
}