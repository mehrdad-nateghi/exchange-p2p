<?php

namespace App\Http\Requests;

use App\Rules\FeasibilityThresholdRange;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequestRequest extends FormRequest
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
            'type' => 'required|in:0,1',
            'trade_volume' => 'required|numeric',
            'lower_bound_feasibility_threshold' => 'required|numeric',
            'upper_bound_feasibility_threshold' => 'required|numeric',
            'description' => 'string',
            'acceptance_threshold' => ['required', new FeasibilityThresholdRange],
            'request_rate' => ['required', new FeasibilityThresholdRange],
            'request_payment_methods' => 'required|array|min:1',
            'request_payment_methods.*' => 'integer',
            'applicant_id' => 'required'
        ];
    }
}
