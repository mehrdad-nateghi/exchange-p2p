<?php

namespace App\Http\Requests\API\V1\Trade\User;

use App\Models\DepositReason;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTradeRequest extends FormRequest
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
        $tradeNumber = $this->trade->number;
        $validReasons = DepositReason::pluck('title')
            ->map(fn($reason) => "{$tradeNumber} / {$reason}")
            ->push($tradeNumber)
            ->toArray();

        return [
            'deposit_reason' => [
                'required',
                'string',
                Rule::in($validReasons),
            ],
        ];
    }
}
