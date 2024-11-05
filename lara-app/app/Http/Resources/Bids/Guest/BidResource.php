<?php

namespace App\Http\Resources\Bids\Guest;

use App\Http\Resources\PaymentMethod\Guest\PaymentMethodResource;
use App\Http\Resources\Requests\Guest\RequestResource;
use App\Http\Resources\Users\Guest\UserResource;
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
            'is_best_price' => $this->is_best_price,
            'status' => $this->status->key(),
            'request' => $this->whenLoaded('request', fn() => new RequestResource($this->request)),
            'user' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
            'payment_method' => $this->whenLoaded('paymentMethod', fn() => new PaymentMethodResource($this->paymentMethod)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            //'deleted_at' => $this->deleted_at
        ];
    }
}
