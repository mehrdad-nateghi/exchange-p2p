<?php

namespace App\Http\Requests\API\V1\Request\User;

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

class StoreRequestRequest extends FormRequest
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
            'type' => [
                'required',
                Rule::enum(RequestTypeEnum::class)
            ],

            'volume' => [
                'required', 'numeric' // TODO-MN: max?
            ],

            'price' => [
                // TODO-MN: max?
                'required',
                'numeric',
                'gte:min_allowed_price',
                'lte:max_allowed_price'
            ],

            // todo: in: paypal and foreign bank
            'payment_methods' => [
                'required', 'array' // todo: payment method is active?
            ],

            'payment_methods.*' => [
                'required', 'string',
                'exists:payment_methods,ulid',
                Rule::exists('payment_methods', 'ulid')->where(function ($query) {
                    $query->where('user_id', Auth::id())->whereIn('type',[PaymentMethodTypeEnum::PAYPAL->value, PaymentMethodTypeEnum::FOREIGN_BANK->value]);
                }),
            ],

            'min_allowed_price' => [
                'required',
                'numeric',
            ],

            'max_allowed_price' => [
                'required',
                'numeric',
            ],

            'status' => [
                'required',
                'numeric',
                Rule::in([RequestStatusEnum::PENDING->value])
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        // get min & max allowed price
        $prices = getMinMaxAllowedPrice();

        $this->merge([
            'min_allowed_price' => $prices['min'],
            'max_allowed_price' => $prices['max'],
            'status' => RequestStatusEnum::PENDING->value,
        ]);
    }
}
