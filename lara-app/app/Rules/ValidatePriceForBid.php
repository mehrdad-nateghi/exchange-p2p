<?php

namespace App\Rules;

use App\Models\Request;
use Illuminate\Contracts\Validation\Rule;

class ValidatePriceForBid implements Rule
{
    protected $requestUlid;
    private string $errorMessage = '';

    public function __construct($requestUlid)
    {
        $this->requestUlid = $requestUlid;
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
        $request = Request::where('ulid', $this->requestUlid)->first();

        if (!$request) {
            $this->errorMessage = 'The selected request for price is invalid.';
            return false;
        }

        $latestBid = $request->bids()->latest()->first();

        $value = (int) $value;
        $requestPrice = (int) $request->price;
        $minAllowedPrice = (int) $request->min_allowed_price;

        if (empty($latestBid) && ($value < $minAllowedPrice || $value > $requestPrice)) {
            $this->errorMessage = __('api-messages.bid_price_must_between', ['min' => $minAllowedPrice, 'max' => $requestPrice]);
            return false;
        }

        if(!empty($latestBid)){
            $maxPrice = min($latestBid->price + config('constants.BID_PRICE_PLUS_LATEST_BID_PRICE_RIAL'), $requestPrice);

            if ($value < $minAllowedPrice || $value <= $maxPrice || $value > $requestPrice) {
                $this->errorMessage = __('api-messages.bid_price_must_between', ['min' => $maxPrice, 'max' => $requestPrice]);
                return false;
            }
        }

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
