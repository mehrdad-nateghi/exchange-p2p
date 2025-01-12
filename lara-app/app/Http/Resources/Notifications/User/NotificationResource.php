<?php

namespace App\Http\Resources\Notifications\User;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'message' => $this->notifiable->getNotificationMessage($this),
            'info' => [
                'icon' => $this->data['info']['icon'] ?? '',
                'model' => $this->data['info']['model'] ?? ['ulid' => '','name' => ''],
            ],
            'created_at' => $this->created_at,
            'read_at' => $this->read_at,
        ];
    }
}
