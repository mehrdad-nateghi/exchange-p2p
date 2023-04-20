<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
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
            'trade_volume' => $this->trade_volume,
            'lower_bound_feasibility_threshold' => (string) $this->lower_bound_feasibility_threshold,
            'upper_bound_feasibility_threshold' => (string) $this->upper_bound_feasibility_threshold,
            'acceptance_threshold' => $this->acceptance_threshold,
            'status' => $this->status,
            'description' => $this->description,
            'payment_reason' => $this->payment_reason,
            'is_removed' => $this->is_removed,
            'created_at' => $this->created_at,
            'applicant_id' => $this->applicant_id
          ];
    }
}
