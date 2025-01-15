<?php

namespace App\Http\Requests\API\V1\Bid\Users;

use App\Enums\BidStatusEnum;
use App\Enums\RequestStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcceptBidRequest extends FormRequest
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
            'request_status' => [
                'required',
                Rule::in([RequestStatusEnum::PENDING->value, RequestStatusEnum::PROCESSING->value])
            ],
            'bid_status' => [
                'required',
                Rule::in([BidStatusEnum::REGISTERED->value])
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'request_status' => $this->bid->request->status->value,
            'bid_status' => $this->bid->status->value,
        ]);
    }
}
