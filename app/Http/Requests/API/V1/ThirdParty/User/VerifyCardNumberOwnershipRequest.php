<?php

namespace App\Http\Requests\API\V1\ThirdParty\User;

use Illuminate\Foundation\Http\FormRequest;

class VerifyCardNumberOwnershipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'card_number' => [
                'required',
                'ir_bank_card_number',
                'unique:rial_bank_accounts,card_number'
            ],

            'national_code' => [
                'required',
                'ir_national_code',
                'exists:users,national_code'
            ]
        ];
    }

    protected function prepareForValidation()
    {
        $user = auth()->user();

        $this->merge([
            'national_code' => $user?->national_code
        ]);
    }
}
