<?php

namespace App\Http\Requests\API\V1\PaymentMethod;

use App\Enums\PaymentMethodTypeEnum;
use App\Enums\VerificationCodeTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Intervention\Validation\Rules\Bic;
use Intervention\Validation\Rules\Iban;

class StorePaymentMethodRequest extends FormRequest
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
            // PAYMENT METHODS
            'type' => [
                'required',
                Rule::enum(PaymentMethodTypeEnum::class)
            ],

            // RIAL ACCOUNTS
            'card_number' => [
                'bail',
                'required_if:type,' . PaymentMethodTypeEnum::RIAL_BANK->value,
                'ir_bank_card_number',
                'unique:rial_bank_accounts,card_number'
            ],
            'sheba' => [
                'bail',
                'required_if:type,' . PaymentMethodTypeEnum::RIAL_BANK->value,
                'ir_sheba',
                'unique:rial_bank_accounts,sheba'
            ],
            'account_no' => [
                'bail',
                'nullable',
                'string',
                'max:50',
                'unique:rial_bank_accounts,account_no'
            ],

            // FOREIGN ACCOUNTS
            'iban' => [
                'bail',
                'required_if:type,' . PaymentMethodTypeEnum::FOREIGN_BANK->value,
                new Iban(),
                'unique:foreign_bank_accounts,iban'
            ],
            'bic' => [
                'bail',
                'required_if:type,' . PaymentMethodTypeEnum::FOREIGN_BANK->value,
                new Bic(),
                'unique:foreign_bank_accounts,bic'
            ],

            // PAYPAL ACCOUNTS
            'email' => [
                'bail',
                'required_if:type,' . PaymentMethodTypeEnum::PAYPAL->value,
                'email',
                'unique:paypal_accounts,email'
            ],

            // Common
            'holder_name' => [
                'bail',
                'required',
                'string',
                'max:50',
            ],
            'bank_name' => [
                'bail',
                Rule::requiredIf(function () {
                    return in_array(request('type'), [
                        PaymentMethodTypeEnum::RIAL_BANK->value,
                        PaymentMethodTypeEnum::FOREIGN_BANK->value,
                    ]);
                }),
                'string',
                'max:50',
            ],
            'is_active' => [
                'bail',
                'required',
                'boolean',
            ],
        ];
    }
}
