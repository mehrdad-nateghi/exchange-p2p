<?php

namespace App\Http\Requests\API\V1\Bid\Users;

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

class IndexBidRequest extends FormRequest
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

            'filter.status' => [
                'nullable',
                'in:active,inactive'
            ],

            'sort' => [
                'nullable',
                'in:created_at,-created_at',
            ]
        ];
    }
}
