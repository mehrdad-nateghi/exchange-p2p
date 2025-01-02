<?php

namespace App\Http\Requests\API\V1\File\User;

use App\Enums\FileStatusEnum;
use App\Enums\PaymentMethodTypeEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Enums\TradeStepsStatusEnum;
use App\Enums\VerificationCodeViaEnum;
use App\Models\RialBankAccount;
use App\Rules\AlphaSpace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Intervention\Validation\Rules\Bic;
use Intervention\Validation\Rules\Iban;

class UploadReceiptRequest extends FormRequest
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
            'receipt' => [
                'required',
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:10240', // 10MB max file size
                $this->validateNoExistingReceipt(),
            ],

            'rial_bank_account' => [
                'required',
                'boolean',
                Rule::in([true]),
            ],
        ];
    }

    protected function validateNoExistingReceipt()
    {
        return function ($attribute, $value, $fail) {
            $existingReceipt = $this->tradeStep->files()
                ->where('status', FileStatusEnum::UPLOADED->value)
                ->exists();

            if ($existingReceipt) {
                $fail(__('validation.receipt_already_exists'));
            }
        };
    }

    protected function prepareForValidation(): void
    {
        $tradeStep = $this->tradeStep;
        $request = $tradeStep->request;

        $hasRialBank = $request->paymentMethods()
            ->where('payment_method_type', RialBankAccount::class)
            ->exists();

        $this->merge([
            'rial_bank_account' => $hasRialBank,
        ]);
    }

    public function messages(): array
    {
        return [
            'rial_bank_account.in' =>  __('validation.request_must_have_rial_account'),
        ];
    }
}
