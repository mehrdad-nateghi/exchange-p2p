<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CardToIbanSuccessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $result = $this['result'];
        return [
            "trackId" => $this['trackId'],
            "iban" => $result['IBAN'],
            "card" => $result['card'],
            "deposit" => $result['deposit'],
            "bank_name" => $result['bankName'],
            //"deposit_description" => $result['depositDescription'],
            "deposit_owners" => $result['depositOwners']
        ];
    }
}
