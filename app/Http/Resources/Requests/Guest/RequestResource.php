<?php

namespace App\Http\Resources\Requests\Guest;

use App\Http\Resources\Bids\Guest\BidCollection;
use App\Http\Resources\PaymentMethod\Guest\PaymentMethodCollection;
use App\Http\Resources\Users\Guest\UserResource;
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
            'trade_ulid' => $this->whenLoaded('latestTradeWithTrashed', fn() => $this->latestTradeWithTrashed?->ulid),
            'user' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
            'bids' => $this->whenLoaded('bids', fn() => BidCollection::make($this->bids)),
            'payment_methods' => $this->whenLoaded('paymentMethods', fn() => PaymentMethodCollection::make($this->paymentMethods)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
