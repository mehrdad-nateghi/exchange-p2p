<?php

namespace App\Http\Resources;

use App\Enums\PaymentMethodTypeEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'ulid' => $this->ulid,
            'user' => new UserResource($this->whenLoaded('user')),
            'payment_method' => $this->getPaymentMethodResource(),
          ];
    }

    private function getPaymentMethodResource(): RialBankAccountResource|ForeignBankAccountResource|PaypalAccountResource|null
    {
        return match ($this->type) {
            PaymentMethodTypeEnum::RIAL_BANK->value => new RialBankAccountResource($this->paymentMethod),
            PaymentMethodTypeEnum::FOREIGN_BANK->value => new ForeignBankAccountResource($this->paymentMethod),
            PaymentMethodTypeEnum::PAYPAL->value => new PaypalAccountResource($this->paymentMethod),
            default => null,
        };
    }
}
