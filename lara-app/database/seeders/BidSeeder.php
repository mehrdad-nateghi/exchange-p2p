<?php

namespace Database\Seeders;

use App\Enums\BidStatusEnum;
use App\Enums\InvoiceStatusEnum;
use App\Enums\InvoiceTypeEnum;
use App\Enums\PaymentMethodTypeEnum;
use App\Enums\RequestTypeEnum;
use App\Enums\TradeStatusEnum;
use App\Enums\TradeStepsStatusEnum;
use App\Models\Bid;
use App\Models\Request;
use App\Models\Step;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BidSeeder extends Seeder
{
    public function run()
    {
        // get all requests
        $requests = Request::all();

        foreach ($requests as $request) {
            // Get Bidder
            $requesterEmail = $request->user->email;
            $bidderEmail = str_replace('requester', 'bidder', $requesterEmail);
            $bidder = User::where('email', $bidderEmail)->first();

            $paymentMethod = $bidder->paymentMethods()
                ->whereIn('type', [
                    PaymentMethodTypeEnum::FOREIGN_BANK->value,
                    PaymentMethodTypeEnum::PAYPAL->value
                ])
                ->inRandomOrder()
                ->first();

            // get 4 random bidders excluding the main bidder
            $otherBidders = User::where('email', 'like', '%bidder%')
                ->where('id', '!=', $bidder->id)
                ->inRandomOrder()
                ->take(4)
                ->get();

            foreach ($otherBidders as $otherBidder) {
                $otherPaymentMethod = $otherBidder->paymentMethods()
                    ->whereIn('type', [
                        PaymentMethodTypeEnum::FOREIGN_BANK->value,
                        PaymentMethodTypeEnum::PAYPAL->value
                    ])
                    ->inRandomOrder()
                    ->first();

                // Get latest bid for this request
                $latestBid = $request->bids()->latest()->first();

                if (empty($latestBid)) {
                    // First bid should be less than request price
                    $randomPrice = rand($request->min_allowed_price, $request->price - 1);
                } else {
                    // Subsequent bids
                    $minPrice = $latestBid->price + config('constants.BID_PRICE_PLUS_LATEST_BID_PRICE_RIAL') + 1;
                    $maxPrice = $request->price - 1;

                    if ($minPrice < $maxPrice) {
                        $randomPrice = rand($minPrice, $maxPrice);
                    } else {
                        continue; // Skip if no valid price range
                    }
                }

                Bid::create([
                    'user_id' => $otherBidder->id,
                    'request_id' => $request->id,
                    'payment_method_id' => $otherPaymentMethod->id,
                    'price' => $randomPrice,
                    'status' => BidStatusEnum::REJECTED->value,
                ]);
            }

            // Finally, create the accepted bid with the request price
            $bid = Bid::create([
                'user_id' => $bidder->id,
                'request_id' => $request->id,
                'payment_method_id' => $paymentMethod->id,
                'price' => $request->price,
                'status' => BidStatusEnum::ACCEPTED->value,
            ]);

            $trade = $bid->trades()->create([
                'request_id' => $bid->request_id,
                'status' => TradeStatusEnum::PROCESSING->value,
            ]);

            // create trade steps
            $steps = Step::all();

            $stepsData = $steps->map(function ($step) {
                return [
                    'name' => $step->name,
                    'description' => $step->description,
                    'priority' => $step->priority,
                    'owner' => $step->owner,
                    'status' => $step->name === 'Pay Toman to System' ? TradeStepsStatusEnum::DOING->value : TradeStepsStatusEnum::TODO->value,
                    'duration_minutes' => $step->duration_minutes,
                    'expire_at' => $step->name === 'Pay Toman to System' ? Carbon::now()->addMinute($step->duration_minutes) : null,
                ];
            })->toArray();

            $trade->tradeSteps()->createMany($stepsData);
            $request = $bid->request;

            // create invoice for trade
            $amount = $bid->refresh()->price * $request->volume;
            $feePercentage = config('constants.invoice_fee_percentage');
            $fee = round($amount * ($feePercentage / 100), 2);

            $userId = $request->type->value == RequestTypeEnum::BUY->value ? $request->user_id : $bid->user_id;

            $trade->refresh()->invoices()->create([
                'user_id' => $userId,
                'amount' => $amount,
                'fee' => $fee,
                'status' => InvoiceStatusEnum::PENDING->value,
                'type' => InvoiceTypeEnum::STEP_ONE_PAY_TOMAN_TO_SYSTEM->value,
            ]);
        }
    }
}
