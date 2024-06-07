<?php

namespace App\Http\Resources;

use App\Http\Resources\V2\Candidates\CandidateFilterResource;
use App\Http\Resources\V2\Candidates\CandidateResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaymentMethodCollection extends ResourceCollection
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
            'items' =>  PaymentMethodResource::collection($this),
            'pagination' => generatePaginationParams($this),
        ];
    }
}
