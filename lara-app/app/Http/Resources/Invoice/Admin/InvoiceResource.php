<?php

namespace App\Http\Resources\Invoice\Admin;

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
            'status' => $this->status->key(),
            'type' => $this->type->key(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
