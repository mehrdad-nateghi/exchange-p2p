<?php

namespace App\Http\Resources;

use App\Enums\PaymentMethodTypeEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class ForeignBankAccountResource extends JsonResource
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
            'type' => PaymentMethodTypeEnum::FOREIGN_BANK->key(),
            'holder_name' => $this->holder_name,
            'bank_name' => $this->bank_name,
            'iban' => $this->iban,
            'bic' => $this->bic,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
          ];
    }
}
