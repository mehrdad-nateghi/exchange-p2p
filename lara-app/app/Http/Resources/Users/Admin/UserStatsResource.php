<?php

namespace App\Http\Resources\Users\Admin;

use App\Http\Resources\Roles\Admin\RoleCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserStatsResource extends JsonResource
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
            'active_requests' => $this->active_requests,
            'bids' => $this->bids()->count(),
            'completed_trades' => $this->completed_trades,
          ];
    }
}
