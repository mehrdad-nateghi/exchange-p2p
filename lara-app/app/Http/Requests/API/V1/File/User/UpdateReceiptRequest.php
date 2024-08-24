<?php

namespace App\Http\Requests\API\V1\File\User;

use App\Enums\FileStatusEnum;
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

class UpdateReceiptRequest extends FormRequest
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
                'required',
                'in:accept,reject',
            ],
        ];
    }

    /*protected function prepareForValidation(): void
    {
        $step = $this->step;

        $this->merge([
            'step_status' => $step->status->value,
        ]);
    }*/

    protected function passedValidation(): void
    {
        $v =  $this->status === 'accept' ? FileStatusEnum::ACCEPT_BY_BUYER->value : FileStatusEnum::REJECT_BY_BUYER->value;
        $this->replace([
            'status' => $v,
        ]);
    }


}
