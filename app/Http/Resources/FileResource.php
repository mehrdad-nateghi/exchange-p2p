<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FileResource extends JsonResource
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
            'path' => $this->path,
            'url' => $this->when($this->shouldIncludeUrl(), $this->url),
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'status' => $this->status->key(),
            'uploader' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    protected function shouldIncludeUrl()
    {
        return request()->route()->getName() === 'V1.users.files.show';
    }
}
