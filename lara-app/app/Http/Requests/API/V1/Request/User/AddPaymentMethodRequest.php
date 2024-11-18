<?php

namespace App\Http\Requests\API\V1\Request\User;

use App\Enums\PaymentMethodTypeEnum;
use App\Enums\RequestStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddPaymentMethodRequest extends FormRequest
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
            'request_status' => [
                'required',
                Rule::in([RequestStatusEnum::TRADING->value])
            ],

            'payment_method_status' => [
                'required',
                'boolean',
                Rule::in([true]),
            ],

            'payment_method_type' => [
                'required',
                Rule::in([PaymentMethodTypeEnum::RIAL_BANK->value])
            ],

            'payment_method' => [
                'required',
                'boolean',
                Rule::in([true]),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $request = $this->route('request');
        $paymentMethod = $this->paymentMethod;

        $hasRialBankMethod = $request->paymentMethods()
            ->where('type', PaymentMethodTypeEnum::RIAL_BANK->value)
            ->doesntExist();

        $this->merge([
            'request_status' => $request->status->value,
            'payment_method_status' => $paymentMethod->paymentMethod->is_active,
            'payment_method_type' => $paymentMethod->type->value,
            'payment_method' => $hasRialBankMethod,
        ]);
    }

    public function messages(): array
    {
        return [
            'payment_method.in' => 'A Rial Bank payment method already exists for this request. You cannot add another one.',
        ];
    }
}
