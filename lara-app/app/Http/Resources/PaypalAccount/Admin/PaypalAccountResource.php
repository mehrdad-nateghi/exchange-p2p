<?php

namespace App\Http\Resources\PaypalAccount\Admin;

use App\Enums\PaymentMethodTypeEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class PaypalAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            //'ulid' => $this->ulid,
            'type' => PaymentMethodTypeEnum::PAYPAL->key(),
            'holder_name' => $this->holder_name,
            'email' => $this->email,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
          ];
    }
}
