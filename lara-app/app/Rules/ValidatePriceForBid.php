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

        $latestBid = $request->bids()->latest()->first();

        if (empty($latestBid) && ($value < $request->min_allowed_price || $value > $request->price)) {
            $this->errorMessage = __('api-messages.bid_price_must_between', ['min' => $request->min_allowed_price, 'max' => $request->price]);
            return false;
        }

        if(!empty($latestBid)){
            $maxPrice = min($latestBid->price + config('constants.BID_PRICE_PLUS_LATEST_BID_PRICE_RIAL'), $request->price);

            if ($value < $request->min_allowed_price || $value <= $maxPrice || $value > $request->price) {
                $this->errorMessage = __('api-messages.bid_price_must_between', ['min' => $maxPrice, 'max' => $request->price]);
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
