<?php

namespace App\Http\Requests\API\V1\Bid\Users;

use App\Enums\BidStatusEnum;
use App\Models\PaymentMethod;
use App\Models\Request;
use App\Rules\ValidatePaymentMethodForBid;
use App\Rules\ValidatePriceForBid;
use App\Rules\ValidateRequestForBid;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBidRequest extends FormRequest
{
    private Request|null $requestModel = null;

    public function authorize()
    {
        if ($this->has('request')) {
            $this->requestModel = Request::where('ulid', $this->input('request'))->first();
        }

        if (!$this->requestModel) {
            return false;
        }

        $user = Auth::user();
        return $user->id != $this->requestModel->user_id;
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
                new ValidateRequestForBid($this->requestModel)
            ],

            'payment_method' => [
                'bail',
                'required',
                'exists:payment_methods,ulid',
                new ValidatePaymentMethodForBid()
            ],

            'price' => [
                'bail',
                'required',
                new ValidatePriceForBid($this->requestModel)
            ],

        ];
    }

    protected function passedValidation(): void
    {
        $this->replace([
            'request_id' => $this->requestModel->id,
            'must_accept_bid' => $this->integer('price') === (int) $this->requestModel->price,
            'payment_method_id' => PaymentMethod::where('ulid', $this->input('payment_method'))->first()->id,
            'status' => $this->getStatus(),
        ]);
    }

    private function getStatus():int
    {
        $requestPrice = $this->requestModel->price;
        if($requestPrice == $this->input('price')){
            return BidStatusEnum::ACCEPTED->value;
        }

        return BidStatusEnum::REGISTERED->value;
    }
}
