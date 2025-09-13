<?php

namespace App\Http\Requests\API\V1\PaymentMethod\User;

use App\Rules\PaymentMethodIsInUse;
use Illuminate\Foundation\Http\FormRequest;

class DeletePaymentMethodRequest extends FormRequest
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
            'payment_method' => ['required',new PaymentMethodIsInUse($this->paymentMethod)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'payment_method' => $this->paymentMethod,
        ]);
    }
}
