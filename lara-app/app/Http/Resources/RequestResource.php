<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
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
            'volume' => $this->volume,
            'price' => $this->price,
            'type' => $this->type->key(),
            'status' => $this->status->key(),
            'user' => new UserResource($this->user),
            'user_role_on_request' => $this->user_role_on_request,
            'payment_methods' => PaymentMethodCollection::make($this->paymentMethods),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
