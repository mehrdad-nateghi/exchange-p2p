<?php

namespace App\Http\Resources\PaymentMethod\Admin;

use App\Enums\PaymentMethodTypeEnum;
use App\Http\Resources\ForeignBankAccount\Admin\ForeignBankAccountResource;
use App\Http\Resources\PaypalAccount\Admin\PaypalAccountResource;
use App\Http\Resources\RialBankAccount\Admin\RialBankAccountResource;
use App\Http\Resources\Users\Admin\UserResource;
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

    private function getPaymentMethodResource(): RialBankAccountResource|ForeignBankAccountResource|PaypalAccountResource
    {
        return match (intval($this->type->value)) {
            PaymentMethodTypeEnum::RIAL_BANK->value => new RialBankAccountResource($this->paymentMethod),
            PaymentMethodTypeEnum::FOREIGN_BANK->value => new ForeignBankAccountResource($this->paymentMethod),
            PaymentMethodTypeEnum::PAYPAL->value => new PaypalAccountResource($this->paymentMethod),
        };
    }
}
