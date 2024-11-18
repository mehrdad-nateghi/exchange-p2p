<?php

namespace App\Http\Resources\Requests\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RequestCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'items' =>  RequestResource::collection($this),
            'pagination' => generatePaginationParams($this),
        ];
    }
}
