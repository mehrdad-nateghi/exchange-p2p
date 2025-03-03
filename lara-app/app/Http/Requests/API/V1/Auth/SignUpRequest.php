<?php

namespace App\Http\Requests\API\V1\Auth;

use App\Enums\UserStatusEnum;
use App\Enums\VerificationCodeTypeEnum;
use App\Enums\VerificationCodeViaEnum;
use App\Rules\CodeValid;
use App\Rules\CodeValidRule;
use App\Rules\VerificationCodeNotExpiredRule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SignUpRequest extends FormRequest
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
            'code' => ['required', 'string', new CodeValidRule($to, $via, $type)],
            'to' => [
                'bail',
                'required',
                Rule::when($via === VerificationCodeViaEnum::EMAIL->value, fn() => [
                    'email:filter',
                    'unique:users,email',
                ]),
                Rule::when($via === VerificationCodeViaEnum::MOBILE->value, fn() => [
                    'ir_mobile:zero',
                    'unique:users,mobile',
                ])
            ],
            'via' => [
                'required',
                Rule::in([
                    VerificationCodeViaEnum::EMAIL->value,
                    VerificationCodeViaEnum::MOBILE->value
                ])
            ],
            'type' => $this->getTypeRules($via),
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

    /*protected function passedValidation(): void
    {
        $validated = $this->validated();
        $via = (int) $validated['via'];
        $to = $validated['to'];

        $userData = [];

        if ($via === VerificationCodeViaEnum::EMAIL->value) {
            $userData['email'] = $to;
            $userData['email_verified_at'] = Carbon::now();
        } elseif ($via === VerificationCodeViaEnum::MOBILE->value) {
            $userData['mobile'] = $to;
            $userData['mobile_verified_at'] = Carbon::now();
        }

        $userData['status'] = UserStatusEnum::ACTIVE->value;

        $this->replace(array_merge($validated, $userData));
    }*/

    /**
     * Get only the user data needed for creation
     *
     * @return array
     */
    public function getUserData(): array
    {
        $userData = [];
        $via = (int) $this->validated('via');
        $to = $this->validated('to');

        // Set user contact info
        if ($via === VerificationCodeViaEnum::EMAIL->value) {
            $userData['email'] = $to;
            $userData['email_verified_at'] = Carbon::now();
        } elseif ($via === VerificationCodeViaEnum::MOBILE->value) {
            $userData['mobile'] = $to;
            $userData['mobile_verified_at'] = Carbon::now();
        }

        // Set status
        $userData['status'] = UserStatusEnum::ACTIVE->value;

        return $userData;
    }

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
