<?php

namespace App\Http\Resources\Trades\Guest;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TradeCollection extends ResourceCollection
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
            'items' =>  TradeResource::collection($this),
            'pagination' => generatePaginationParams($this),
        ];
    }
}
