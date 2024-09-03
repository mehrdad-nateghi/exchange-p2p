<?php

namespace App\Services\API\V1;

use App\Enums\BidStatusEnum;
use App\Models\Bid;
use App\Models\PaymentMethod;
use App\Models\Request;
use App\Models\Trade;

class TradeService
{
    private Trade $model;


    public function __construct(Trade $model)
    {
        $this->model = $model;
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($trade,$data)
    {
        return $trade->update($data);
    }
}
