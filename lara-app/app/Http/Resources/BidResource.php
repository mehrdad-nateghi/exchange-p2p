<?php

namespace App\Http\Resources;

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
            'id' => $this->id,
            'type' => $this->type,
            'bid_rate' => $this->bid_rate,
            'status' => $this->status,
            'description' => $this->description,
            'request_id' => $this->request_id,
            'applicant_id' => $this->applicant_id,
            'created_at' => $this->created_at,
          ];
    }
}
