<?php

namespace App\Rules;

use App\Enums\RequestTypeEnum;
use App\Models\Request;
use Illuminate\Contracts\Validation\Rule;

class ValidatePriceForBid implements Rule
{
    private string $errorMessage = '';

    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function passes($attribute, $value)
    {
        if (!$this->request) {
            $this->errorMessage = 'The selected request for price is invalid.';
            return false;
        }

        $latestBid = $this->request->bids()->latest()->first();

        $value = (int) $value;
        $requestPrice = (int) $this->request->price;
        $minAllowedPrice = (int) $this->request->min_allowed_price;
        $maxAllowedPrice = (int) $this->request->max_allowed_price;
        $isBuyRequest = $this->request->type->value === RequestTypeEnum::BUY->value;

        if (empty($latestBid)) {
            if ($isBuyRequest) {
                // For BUY: First bid should be between request_price and max_allowed_price (exclusive request_price)
                if ($value < $requestPrice || $value > $maxAllowedPrice) {
                    $this->errorMessage = __('api-messages.bid_price_must_between', ['min' => $requestPrice, 'max' => $maxAllowedPrice]);
                    return false;
                }
            } else {
                // For SELL: First bid should be between min_allowed_price and request_price (exclusive request_price)
                if ($value > $requestPrice || $value < $minAllowedPrice) {
                    $this->errorMessage = __('api-messages.bid_price_must_between', ['min' => $minAllowedPrice, 'max' => $requestPrice]);
                    return false;
                }
            }
        }

        if (!empty($latestBid)) {
            if ($isBuyRequest) {
                $maxPrice = $latestBid->price - config('constants.bid_price_plus_latest_bid_price_rial');
                // For BUY: New bid must be lower than last bid but higher than request_price
                if ($value < $requestPrice || $value > $maxPrice) {
                    $this->errorMessage = __('api-messages.bid_price_must_between', ['min' => $requestPrice, 'max' => $maxPrice]);
                    return false;
                }
            } else {
                $minPrice = $latestBid->price + config('constants.bid_price_plus_latest_bid_price_rial');
                // For SELL: New bid must be higher than last bid but lower than request_price
                if ($value > $requestPrice || $value < $minPrice) {
                    $this->errorMessage = __('api-messages.bid_price_must_between', ['min' => $minPrice, 'max' => $requestPrice]);
                    return false;
                }
            }
        }

        return true;
    }

    public function message()
    {
        return $this->errorMessage;
    }
}
