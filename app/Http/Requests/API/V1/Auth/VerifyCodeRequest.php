<?php

namespace App\Http\Requests\API\V1\Auth;

use App\Enums\VerificationCodeTypeEnum;
use App\Enums\VerificationCodeViaEnum;
use App\Rules\CodeValid;
use App\Rules\CodeValidRule;
use App\Rules\VerificationCodeNotExpiredRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyCodeRequest extends FormRequest
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
        $to = $this->request->get('to');
        $via = $this->request->getInt('via');
        $type = $this->request->getInt('type');

        return [
            'code' => ['required','string',new CodeValidRule($to, $via, $type)],
            'to' => [
                'bail',
                'required',
                'exists:verification_codes,to',
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

//            'to' => [
//                'required',
//                Rule::when($via === VerificationCodeViaEnum::EMAIL->value,fn() => [
//                    'email:filter',
//                    'exists:users,email'
//                ]),
//            ],
//            'via' => ['required',Rule::In(VerificationCodeViaEnum::EMAIL->value)],
//            'type' => ['required',Rule::In(VerificationCodeTypeEnum::RESET_PASSWORD->value)],
        ];
    }
}
