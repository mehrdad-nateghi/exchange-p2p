<?php

namespace App\Http\Resources\Bids\Guest;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BidCollection extends ResourceCollection
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
            'items' =>  BidResource::collection($this),
            'pagination' => generatePaginationParams($this),
        ];
    }
}
