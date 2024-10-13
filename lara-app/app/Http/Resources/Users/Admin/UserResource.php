<?php

namespace App\Http\Resources\Users\Admin;

use App\Http\Resources\Roles\Admin\RoleCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'status' => $this->status,
            'role_on_request' => $this->user_role_on_request,
            'stats' => new UserStatsResource($this),
            'roles' => RoleCollection::make($this->roles),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
          ];
    }
}
