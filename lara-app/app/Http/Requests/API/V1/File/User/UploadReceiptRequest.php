<?php

namespace App\Http\Requests\API\V1\File\User;

use App\Enums\PaymentMethodTypeEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Enums\TradeStepsStatusEnum;
use App\Enums\VerificationCodeViaEnum;
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
            ],

            /*'step_status' => [
                Rule::in([TradeStepsStatusEnum::DOING->value])
            ]*/
        ];
    }

    protected function prepareForValidation(): void
    {
        $step = $this->step;

        $this->merge([
            'step_status' => $step->status->value,
        ]);
    }


}
