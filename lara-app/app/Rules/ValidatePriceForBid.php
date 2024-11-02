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

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
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
        $isBuyRequest = $this->request->type->value === RequestTypeEnum::BUY->value ; // Assuming you have a type field

        if (empty($latestBid)) {
            if ($isBuyRequest) {
                if ($value < $minAllowedPrice || $value > $requestPrice) {
                    $this->errorMessage = __('api-messages.bid_price_must_between', ['min' => $minAllowedPrice, 'max' => $requestPrice]);
                    return false;
                }
            } else { // Sell request
                if ($value > $requestPrice || $value < $minAllowedPrice) {
                    $this->errorMessage = __('api-messages.bid_price_must_between', ['min' => $minAllowedPrice, 'max' => $requestPrice]);
                    return false;
                }
            }
        }

        if (!empty($latestBid)) {
            if ($isBuyRequest) {
                $minPrice = $latestBid->price + config('constants.BID_PRICE_PLUS_LATEST_BID_PRICE_RIAL');
                $maxPrice = $requestPrice;

                if ($value <= $latestBid->price || $value > $maxPrice) {
                    $this->errorMessage = __('api-messages.bid_price_must_between', ['min' => $minPrice, 'max' => $maxPrice]);
                    return false;
                }
            } else { // Sell request
                $maxPrice = $latestBid->price - config('constants.BID_PRICE_PLUS_LATEST_BID_PRICE_RIAL');
                $minPrice = $minAllowedPrice;

                if ($value >= $latestBid->price || $value < $minPrice) {
                    $this->errorMessage = __('api-messages.bid_price_must_between', ['min' => $minPrice, 'max' => $maxPrice]);
                    return false;
                }
            }
        }

        /*if (empty($latestBid) && ($value < $minAllowedPrice || $value > $requestPrice)) {
            $this->errorMessage = __('api-messages.bid_price_must_between', ['min' => $minAllowedPrice, 'max' => $requestPrice]);
            return false;
        }

        if(!empty($latestBid)){
            $maxPrice = min($latestBid->price + config('constants.BID_PRICE_PLUS_LATEST_BID_PRICE_RIAL'), $requestPrice);

            if ($value < $minAllowedPrice || $value <= $maxPrice || $value > $requestPrice) {
                $this->errorMessage = __('api-messages.bid_price_must_between', ['min' => $maxPrice, 'max' => $requestPrice]);
                return false;
            }
        }*/

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage;
    }
}
