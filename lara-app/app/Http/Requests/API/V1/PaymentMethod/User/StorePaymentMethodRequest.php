<?php

namespace App\Http\Requests\API\V1\PaymentMethod\User;

use App\Enums\PaymentMethodTypeEnum;
use App\Rules\AlphaSpace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
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
        $type = $this->request->getInt('type');

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

            'account_no' => [
                'bail',
                'nullable',
                'string',
                'max:50',
                'unique:rial_bank_accounts,account_no'
            ],

            'bank_code' => [
                'bail',
                'string',
                'required_if:type,' . PaymentMethodTypeEnum::RIAL_BANK->value,
            ],

            // FOREIGN ACCOUNTS
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
            'iban' => [
                'string',
                'nullable',
                'required_if:type,' . PaymentMethodTypeEnum::FOREIGN_BANK->value . ',' . PaymentMethodTypeEnum::RIAL_BANK->value,
                function ($attribute, $value, $fail) use ($type) {
                    if ($type == PaymentMethodTypeEnum::RIAL_BANK->value) {
                        $rules = [
                            'ir_sheba',
                            'unique:rial_bank_accounts,iban',
                            'size:26',
                        ];
                    } elseif ($type == PaymentMethodTypeEnum::FOREIGN_BANK->value) {
                        $rules = [
                            new Iban(),
                            'unique:foreign_bank_accounts,iban'
                        ];
                    } else {
                        return; // No validation if type doesn't match
                    }

                    $validator = Validator::make([$attribute => $value], [$attribute => $rules]);
                    if ($validator->fails()) {
                        $fail($validator->errors()->first($attribute));
                    }
                },
            ],

            'holder_name' => [
                'bail',
                'required',
                'string',
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
