<?php

namespace App\Http\Requests\API\V1\Request\Admin;

use App\Enums\PaymentMethodTypeEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Enums\VerificationCodeViaEnum;
use App\Rules\AlphaSpace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Intervention\Validation\Rules\Bic;
use Intervention\Validation\Rules\Iban;

class IndexRequestRequest extends FormRequest
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
                'in:buy,sell'
            ],

            'filter.status' => [
                'nullable',
            ],

            'filter.status.*' => [
                'nullable',
                Rule::in(RequestStatusEnum::keys())
            ],

            'filter.payment_method' => [
                'nullable',
            ],

            'filter.payment_method.*' => [
                'nullable',
                Rule::in(PaymentMethodTypeEnum::keys())
            ],

            'filter.volume_from' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100000',
            ],

            'filter.volume_to' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100000',
            ],

            'filter.price_from' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'filter.price_to' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'sort' => [
                'nullable',
                'in:created_at,-created_at,price,-price',
            ]
        ];
    }
}
