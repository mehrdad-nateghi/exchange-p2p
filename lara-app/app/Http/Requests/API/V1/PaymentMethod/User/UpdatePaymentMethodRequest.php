<?php

namespace App\Http\Requests\API\V1\PaymentMethod\User;

use App\Enums\PaymentMethodTypeEnum;
use App\Rules\AlphaSpace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Intervention\Validation\Rules\Bic;
use Intervention\Validation\Rules\Iban;

class UpdatePaymentMethodRequest extends FormRequest
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
        $type = $this->paymentMethod->type->value;
        $paymentMethodId = $this->paymentMethod->payment_method_id;

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
                'unique:rial_bank_accounts,card_number,' . $paymentMethodId
            ],
            'sheba' => [
                'bail',
                'required_if:type,' . PaymentMethodTypeEnum::RIAL_BANK->value,
                'ir_sheba',
                'unique:rial_bank_accounts,sheba,' . $paymentMethodId
            ],
            'account_no' => [
                'bail',
                'nullable',
                'string',
                'max:50',
                'unique:rial_bank_accounts,account_no,' . $paymentMethodId
            ],

            // FOREIGN ACCOUNTS
            'iban' => [
                'bail',
                'required_if:type,' . PaymentMethodTypeEnum::FOREIGN_BANK->value,
                new Iban(),
                'unique:foreign_bank_accounts,iban,' . $paymentMethodId
            ],
            'bic' => [
                'bail',
                'required_if:type,' . PaymentMethodTypeEnum::FOREIGN_BANK->value,
                new Bic(),
                'unique:foreign_bank_accounts,bic,' . $paymentMethodId
            ],

            // PAYPAL ACCOUNTS
            'email' => [
                'bail',
                'required_if:type,' . PaymentMethodTypeEnum::PAYPAL->value,
                'email',
                'unique:paypal_accounts,email,' . $paymentMethodId
            ],

            // COMMON
            'holder_name' => [
                'bail',
                'required',
                Rule::when(in_array($type, [
                    PaymentMethodTypeEnum::PAYPAL->value,
                    PaymentMethodTypeEnum::FOREIGN_BANK->value,
                ]),fn() => [
                    new AlphaSpace(),
                ]),
                'max:50',
            ],
            'bank_name' => [
                'bail',
                Rule::requiredIf(function () use($type){
                    return in_array($type, [
                        PaymentMethodTypeEnum::RIAL_BANK->value,
                        PaymentMethodTypeEnum::FOREIGN_BANK->value,
                    ]);
                }),
                Rule::when($type === PaymentMethodTypeEnum::FOREIGN_BANK->value,fn() => [
                    new AlphaSpace(),
                ]),
                'max:50',
            ],
            'is_active' => [
                'bail',
                'required',
                'boolean',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'type' => $this->paymentMethod->type->value,
        ]);
    }


}
