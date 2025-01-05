<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'ulid' => $this->ulid,
            'number' => $this->number,
            'amount' => $this->amount,
            'fee' => $this->fee,
            'fee_foreign' => $this->fee_foreign,
            'fee_foreign_currency_code' => $this->fee_foreign_currency_code,
            //'total_payable_amount' => $this->total_payable_amount,
            'status' => $this->status->key(),
            'type' => $this->type->key(),
            /*'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),*/
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
