<?php

namespace App\Http\Resources\Trades\Admin;

use App\Http\Resources\BidResource;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\TradeStepResource;
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
        return [
            'ulid' => $this->ulid,
            'number' => $this->number,
            'deposit_reason' => $this->deposit_reason,
            'deposit_reason_accepted' => $this->deposit_reason_accepted,
            'status' => $this->status->key(),
            'bid' => new BidResource($this->bid),
            'steps' => $this->whenLoaded('tradeSteps', function () {
                return TradeStepResource::collection($this->tradeSteps);
            }),
            'invoices' => $this->whenLoaded('invoices', function () {
                return InvoiceResource::collection($this->invoices);
            }),
            'completed_at' => $this->completed_at,
            'canceled_at' => $this->canceled_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
