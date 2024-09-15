<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TradeStepResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'priority' => $this->priority,
            'owner' => $this->owner->key(),
            //'actions' => $this->actions,
            'status' => $this->status->key(),
            'files' => $this->whenLoaded('files', function () {
                return FileResource::collection($this->files);
            }),
            'expire_at' => $this->expire_at,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
