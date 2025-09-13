<?php

namespace App\Http\Requests\API\V1\Invoice\Admin;

use App\Enums\InvoiceStatusEnum;
use App\Enums\InvoiceTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
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
            'invoice_status' => [
                'required',
                Rule::in([InvoiceStatusEnum::PENDING->value]),
            ],

            'invoice_type' => [
                'required',
                Rule::in([InvoiceTypeEnum::STEP_ONE_PAY_TOMAN_TO_SYSTEM->value]),
            ],

            'ref_id' => [
                'required',
            ],

            'amount' => [
                'required',
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'invoice_status' => $this->invoice->status->value,
            'invoice_type' => $this->invoice->type->value,
        ]);
    }

    public function messages()
    {
        return [
            'invoice_status.in' => __('validation.invoice_status_must_be_pending'),
            'invoice_type.in' => __('validation.invoice_type_must_be_pay_toman_to_system')
        ];
    }
}
