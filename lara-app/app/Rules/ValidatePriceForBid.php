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

    private function getMinPriceDifference(): int
    {
        return config('constants.bid_price_plus_latest_bid_price_rial');
    }

    public function passes($attribute, $value): bool
    {
        if (!$this->validateRequest()) {
            return false;
        }

        $bidPrice = (int) $value;
        $requestPrice = (int) $this->request->price;

        // Auto-accept if bid price equals request price
        if ($bidPrice === $requestPrice) {
            return true;
        }

        $latestBid = $this->request->bids()->latest()->first();

        return $this->validateBidPrice($bidPrice, $latestBid);
    }

    private function validateRequest(): bool
    {
        if (!$this->request) {
            $this->errorMessage = __('validation.invalid_request');
            return false;
        }
        return true;
    }

    private function validateBidPrice(int $bidPrice, $latestBid): bool
    {
        $requestPrice = (int) $this->request->price;
        $minAllowedPrice = (int) $this->request->min_allowed_price;
        $maxAllowedPrice = (int) $this->request->max_allowed_price;
        $isBuyRequest = $this->request->type->value === RequestTypeEnum::BUY->value;

        if (empty($latestBid)) {
            return $this->validateFirstBid($bidPrice, $requestPrice, $minAllowedPrice, $maxAllowedPrice, $isBuyRequest);
        }

        return $this->validateSubsequentBid($bidPrice, $requestPrice, $latestBid, $isBuyRequest);
    }

    private function validateFirstBid(
        int $bidPrice,
        int $requestPrice,
        int $minAllowedPrice,
        int $maxAllowedPrice,
        bool $isBuyRequest
    ): bool {
        if ($isBuyRequest) {
            // BUY: First bid should be lower than max_allowed_price but higher than request_price
            if ($bidPrice < $requestPrice || $bidPrice > $maxAllowedPrice) {
                $this->errorMessage = __('validation.bid_price_must_between', [
                    'min' => $requestPrice,
                    'max' => $maxAllowedPrice
                ]);
                return false;
            }
        } else {
            // SELL: First bid should be higher than min_allowed_price but lower than request_price
            if ($bidPrice > $requestPrice || $bidPrice < $minAllowedPrice) {
                $this->errorMessage = __('validation.bid_price_must_between', [
                    'min' => $minAllowedPrice,
                    'max' => $requestPrice
                ]);
                return false;
            }
        }
        return true;
    }

    private function validateSubsequentBid(
        int $bidPrice,
        int $requestPrice,
            $latestBid,
        bool $isBuyRequest
    ): bool {
        $priceDifference = $this->getMinPriceDifference();

        if ($isBuyRequest) {
            $maxPrice = $latestBid->price - $priceDifference;
            // Ensure maxPrice doesn't exceed the valid range
            $maxPrice = min($maxPrice, $latestBid->price - 1);
            // BUY: New bid must be lower than last bid but higher than request_price
            if ($bidPrice < $requestPrice || $bidPrice > $maxPrice) {
                $this->errorMessage = __('validation.bid_price_must_between', [
                    'min' => $requestPrice,
                    'max' => max($requestPrice + 1, $maxPrice)
                ]);
                return false;
            }
        } else {
            $minPrice = $latestBid->price + $priceDifference;
            // Ensure minPrice doesn't go below the valid range
            $minPrice = max($minPrice, $latestBid->price + 1);
            // SELL: New bid must be higher than last bid but lower than request_price
            if ($bidPrice > $requestPrice || $bidPrice < $minPrice) {
                $this->errorMessage = __('validation.bid_price_must_between', [
                    'min' => min($requestPrice - 1, $minPrice),
                    'max' => $requestPrice
                ]);
                return false;
            }
        }
        return true;
    }

    public function message(): string
    {
        return $this->errorMessage;
    }
}
