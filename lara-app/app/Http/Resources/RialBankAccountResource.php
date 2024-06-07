<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RialBankAccountResource extends JsonResource
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
            'ulid' => $this->ulid,
            'holder_name' => $this->holder_name,
            'bank_name' => $this->bank_name,
            'card_number' => $this->card_number,
            'sheba' => $this->sheba,
            'account_no' => $this->account_no,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
          ];
    }
}
