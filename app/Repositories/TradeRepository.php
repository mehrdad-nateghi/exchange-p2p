<?php

namespace App\Repositories;

use App\Interfaces\TradeRepositoryInterface;
use App\Models\Financial;
use App\Models\Trade;

class TradeRepository implements TradeRepositoryInterface
{

    /*
     * Set system fee to a specific trade
     */
    public function setSystemFee(Trade $trade)
    {

        $trade_volume = $trade->request->trade_volume;

        $system_fee = 0;

        $financial = Financial::first();

        if (!$financial) {
            return false;
        }

        if ($trade_volume > 0 && $trade_volume <= 99) {
            $system_fee = $financial->system_fee_a;
        }
        elseif ($trade_volume >= 100 && $trade_volume <= 999) {
            $system_fee = $financial->system_fee_b;
        }
        elseif ($trade_volume >= 1000 && $trade_volume <= 2999) {
            $system_fee = $financial->system_fee_c;
        }
        elseif ($trade_volume >= 3000) {
            $system_fee = $financial->system_fee_d;
        }
        else{
            return false;
        }

        $trade->update([
            'trade_fee' => $system_fee
        ]);

        return true;

    }

}
