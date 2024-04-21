<?php

namespace App\Interfaces;

use App\Models\Bid;
use App\Models\Request;

interface BidRepositoryInterface
{
    public function confirmBid(Request $request, Bid $bid);
    public function autoConfirmBid(Request $request, Bid $bid);

}
