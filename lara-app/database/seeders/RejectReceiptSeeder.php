<?php

namespace Database\Seeders;

use App\Enums\FileStatusEnum;
use App\Enums\InvoiceStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Enums\TradeStepsStatusEnum;
use App\Enums\TransactionStatusEnum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RejectReceiptSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();

        try {
            $users = User::where('email', 'like', '%requester%')->get();

            $requests = collect();
            foreach ($users as $user) {
                $requests->push($user->requests()->inRandomOrder()->take(1)->first());
            }

            foreach ($requests as $request) {
                $trade = $request->trades()->first();
                $bid = $trade->bid;
                $tradeSteps = $trade->tradeSteps;
                $invoice = $trade->invoices()->first();
                $buyerUserId = $request->type === RequestTypeEnum::BUY->value ? $request->user_id : $bid->user_id;
                $sellerUserId = $request->type === RequestTypeEnum::SELL->value ? $request->user_id : $bid->user_id;

                foreach ($tradeSteps as $tradeStep) {
                    // Step one = pay toman to system
                    if ($tradeStep->priority === 1) {
                        $invoice->transactions()->create([
                            'user_id' => $buyerUserId,
                            'track_id' => fake()->numerify('##########'),
                            'ref_id' => fake()->numerify('####################'),
                            'amount' => $invoice->total_payable_amount,
                            'currency' => 'IRT',
                            'status' => TransactionStatusEnum::COMPLETED->value
                        ]);

                        $invoice->update([
                            'status' => InvoiceStatusEnum::PAID->value
                        ]);

                        $tradeStep->update([
                            'status' => TradeStepsStatusEnum::DONE,
                            'completed_at' => Carbon::now(),
                        ]);
                    }

                    // Step two = Transfer Currency and Upload Receipt
                    if ($tradeStep->priority === 2) {
                        $tradeStep->update([
                            'expire_at' => Carbon::now()->addMinutes($tradeStep->duration_minutes),
                            'status' => TradeStepsStatusEnum::DOING,
                        ]);

                        // Create the record
                        $tradeStep->files()->create([
                            'user_id' => $sellerUserId,
                            'name' => 'default-receipt.png',
                            'path' => 'uploads/default-receipt.png',
                            'mime_type' => 'image/png',
                            'size' => '56000',
                            'status' => FileStatusEnum::REJECT_BY_BUYER,
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error seeding RejectReceiptSeeder: " . $e->getMessage());
        }
    }
}
