<?php

namespace App\Http\Resources\PaymentMethod\Guest;

use App\Enums\PaymentMethodTypeEnum;
use App\Http\Resources\ForeignBankAccount\Guest\ForeignBankAccountResource;
use App\Http\Resources\PaypalAccount\Guest\PaypalAccountResource;
use App\Http\Resources\RialBankAccount\Guest\RialBankAccountResource;
use App\Http\Resources\Users\Guest\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'ulid' => $this->ulid,
            'user' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
            'payment_method' => $this->getPaymentMethodResource(),
          ];
    }

    private function getPaymentMethodResource(): RialBankAccountResource|ForeignBankAccountResource|PaypalAccountResource
    {
        return match (intval($this->type->value)) {
            PaymentMethodTypeEnum::RIAL_BANK->value => new RialBankAccountResource($this->paymentMethod),
            PaymentMethodTypeEnum::FOREIGN_BANK->value => new ForeignBankAccountResource($this->paymentMethod),
            PaymentMethodTypeEnum::PAYPAL->value => new PaypalAccountResource($this->paymentMethod),
        };
    }
}
