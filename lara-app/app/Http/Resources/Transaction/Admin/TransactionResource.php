<?php

namespace App\Http\Resources\Transaction\Admin;

use App\Http\Resources\Invoice\Admin\InvoiceResource;
use App\Http\Resources\Users\Admin\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'track_id' => $this->track_id,
            'ref_id' => $this->ref_id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'provider' => $this->provider->key(),
            'status' => $this->status->key(),
            'metadata' => $this->metadata,
            // Related resources
            'user' => $this->whenLoaded('user',
                fn() => new UserResource($this->user)
            ),
            'invoice' => $this->whenLoaded('invoice',
                fn() => new InvoiceResource($this->invoice)
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
