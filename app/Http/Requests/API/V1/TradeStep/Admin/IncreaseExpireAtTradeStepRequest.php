<?php

namespace App\Http\Requests\API\V1\TradeStep\Admin;

use App\Enums\TradeStepsStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IncreaseExpireAtTradeStepRequest extends FormRequest
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
            'hours' => [
                'nullable',
                'numeric'
            ],

            'status' => [
                'required',
                Rule::in([TradeStepsStatusEnum::DOING->value])
            ],

            'expire_at' => [
                'required',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'hours' => $this->hours ?? config('constants.default_hours_increase_expire_at_trade_step'),
            'status' => $this->tradeStep->status->value,
            'expire_at' => $this->tradeStep->expire_at,
        ]);
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Trade step status must be DOING.',
            'expire_at.required' => 'Trade step expire_at is null.',
        ];
    }
}
