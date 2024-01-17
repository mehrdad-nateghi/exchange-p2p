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
        $data = [
            'id' => $this->id,
            'support_id' => $this->support_id,
            'type' => $this->type,
            'trade_volume' => $this->trade_volume,
            'lower_bound_feasibility_threshold' => (string) $this->lower_bound_feasibility_threshold,
            'upper_bound_feasibility_threshold' => (string) $this->upper_bound_feasibility_threshold,
            'acceptance_threshold' => $this->acceptance_threshold,
            'request_rate' => $this->request_rate,
            'status' => $this->status,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'applicant_id' => $this->applicant_id
          ];

          // Check if request_payment_methods must be included in response
          if(isset($this->request_payment_methods)){
            $data['request_payment_methods'] = $this->request_payment_methods;
          }

          return $data;
    }
}
