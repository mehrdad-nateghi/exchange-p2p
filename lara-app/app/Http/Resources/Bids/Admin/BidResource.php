<?php

namespace App\Http\Resources\Bids\Admin;

use App\Http\Resources\PaymentMethod\Admin\PaymentMethodResource;
use App\Http\Resources\Requests\Admin\RequestResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BidResource extends JsonResource
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
            'number' => $this->number,
            'price' => $this->price,
            'is_highest_price' => $this->is_highest_price,
            'status' => $this->status->key(),
            'payment_method' => new PaymentMethodResource($this->paymentMethod),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
