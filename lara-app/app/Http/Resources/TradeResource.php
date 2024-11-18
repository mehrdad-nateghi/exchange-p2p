<?php

namespace App\Http\Resources;

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
        $owner = $this->bid->request->user_role_on_request;
        return [
            'ulid' => $this->ulid,
            'number' => $this->number,
            'deposit_reason' => $this->deposit_reason,
            'deposit_reason_accepted' => $this->deposit_reason_accepted,
            'status' => $this->getStatusByOwner($owner),
            'bid' => new BidResource($this->bid),
            'steps' => $this->whenLoaded('tradeSteps', function () use($owner){
                return TradeStepResource::collection($this->tradeSteps()->byOwner($owner)->get());
            }),
            'invoices' => $this->whenLoaded('invoices', function () {
                return InvoiceResource::collection($this->invoices);
            }),
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
