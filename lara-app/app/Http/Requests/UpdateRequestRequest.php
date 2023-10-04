<?php

namespace App\Http\Requests;

use App\Rules\FeasibilityThresholdRange;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRequestRequest extends FormRequest
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
            'trade_volume' => 'required|numeric',
            'description' => 'nullable',
            'lower_bound_feasibility_threshold' => 'required|numeric',
            'upper_bound_feasibility_threshold' => 'required|numeric',
            'request_rate' => ['required', new FeasibilityThresholdRange],
            'request_payment_methods' => 'required|array|min:1',
            'request_payment_methods.*' => 'integer'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
