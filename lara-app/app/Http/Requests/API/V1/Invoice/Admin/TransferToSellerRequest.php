<?php

namespace App\Http\Requests\API\V1\Invoice\Admin;

use App\Enums\InvoiceStatusEnum;
use App\Enums\InvoiceTypeEnum;
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

class TransferToSellerRequest extends FormRequest
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
            'status' => [
                Rule::in([InvoiceStatusEnum::PENDING->value])
            ],
            'type' => [
                Rule::in([InvoiceTypeEnum::PAY_TOMAN_TO_SELLER->value])
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->invoice->status->value,
            'type' => $this->invoice->type->value,
        ]);
    }

    public function messages()
    {
        return [
            'status.in' => "The invoice must be in 'pending' status to be paid.",
            'type.in' => "The invoice must be in 'pay toman to seller' type to be paid.",
        ];
    }
}
