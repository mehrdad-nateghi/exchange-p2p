<?php

namespace App\Http\Requests\API\V1\Bid\Users;

use App\Enums\BidStatusEnum;
use App\Enums\RequestStatusEnum;
use App\Models\PaymentMethod;
use App\Models\Request;
use App\Rules\AllowRegisterBid;
use App\Rules\ValidatePaymentMethodForBid;
use App\Rules\ValidatePriceForBid;
use App\Rules\ValidateRequestForBid;
use Illuminate\Foundation\Http\FormRequest;
class StoreBidRequest extends FormRequest
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
            'request' => [
                'bail',
                'required',
                'exists:requests,ulid',
                new ValidateRequestForBid
            ], // todo: this request belongs to current user?

            'payment_method' => [
                'bail',
                'required',
                'exists:payment_methods,ulid',
                new ValidatePaymentMethodForBid($this->input('request'))
            ],

            'price' => [
                'bail',
                'required',
                new ValidatePriceForBid($this->input('request'))
            ],

        ];
    }

    protected function passedValidation(): void
    {
        $request = Request::where('ulid', $this->input('request'))->first();

        $this->replace([
            //'request_model' => $request,
            'request_id' => $request->id,
            'must_accept_bid' => $this->integer('price') , (int) $request->price,
            'payment_method_id' => PaymentMethod::where('ulid', $this->input('payment_method'))->first()->id,
            'status' => $this->getStatus(),
        ]);
    }

    private function getStatus():int
    {
        $requestPrice = Request::find($this->input('request'))->price; // todo: get request in constructor to decrease query.
        if($requestPrice == $this->input('price')){
            return BidStatusEnum::ACCEPTED->value;
        }

        return BidStatusEnum::REGISTERED->value;
    }
}
