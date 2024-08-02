<?php

namespace App\Services\API\V1;

use App\Enums\BidStatusEnum;
use App\Enums\TradeStatusEnum;
use App\Models\Bid;
use App\Models\PaymentMethod;
use App\Models\Request;

class BidService
{
    private Bid $model;


    public function __construct(Bid $model)
    {
        $this->model = $model;
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function acceptBid(Bid $bid): Bid
    {
        $bid->update(['status' => BidStatusEnum::ACCEPTED]);

        $bid->request->bids()
            ->where('id', '!=', $bid->id)
            ->update(['status' => BidStatusEnum::REJECTED]);

        return $bid->fresh();
    }
}
