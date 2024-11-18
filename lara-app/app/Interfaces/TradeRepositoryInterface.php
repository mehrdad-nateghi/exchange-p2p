<?php

namespace App\Interfaces;

use App\Models\Trade;

interface TradeRepositoryInterface
{
    public function setSystemFee(Trade $trade);
}
