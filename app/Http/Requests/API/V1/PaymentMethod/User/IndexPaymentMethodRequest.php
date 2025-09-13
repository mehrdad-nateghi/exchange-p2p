<?php

namespace App\Http\Requests\API\V1\PaymentMethod\User;

use Illuminate\Foundation\Http\FormRequest;

class IndexPaymentMethodRequest extends FormRequest
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
            'filter' => [
                'nullable',
                'array'
            ],

            'filter.type' => [
                'nullable',
                'in:rial,currency'
            ],
        ];
    }
}
