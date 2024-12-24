<?php

namespace App\Http\Resources\Trades\Guest;

use App\Http\Resources\Bids\Guest\BidResource;
use App\Http\Resources\Requests\Guest\RequestResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //$owner = $this->bid->request->user_role_on_request;
        return [
            'ulid' => $this->ulid,
            'number' => $this->number,
            'status' => $this->status->key(),
            'bid' => $this->whenLoaded('bid', function () {
                return new BidResource($this->bid);
            }),
            'request' => $this->whenLoaded('request', function () {
                return new RequestResource($this->request);
            }),
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
