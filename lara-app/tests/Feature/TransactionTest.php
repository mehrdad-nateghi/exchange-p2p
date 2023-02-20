<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\Bid;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\LinkedMethod;
use App\Models\PaymentMethod;
use App\Models\Request;
use App\Models\Trade;
use App\Models\Transaction;
use App\Models\TransactionMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionTest extends TestCase
{

    /** @test for the 1 to n TransactionMethod - Transaction relation*/
    public function a_transaction_belongs_to_a_transactionmethod()
    {
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$paymentMethod->id, 'applicant_id'=>$user->id]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $trade = Trade::factory()->create(['request_id'=>$request->id, 'bid_id'=>$bid->id]);
        $invoice = Invoice::factory()->create(['applicant_id'=>$user->id, 'trade_id'=>$trade->id, 'target_account_id'=>$linkedMethod->id]);
        $transactionMethod = TransactionMethod::factory()->create();
        $transaction = Transaction::factory()->create(['invoice_id'=>$invoice->id, 'transaction_method_id'=>$transactionMethod->id]);

        $this->assertInstanceOf(TransactionMethod::class, $transaction->transactionMethod);
    }

    /** @test for the 1 to n Invoice - Transaction relation*/
    public function a_transaction_belongs_to_a_invoice()
    {
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$paymentMethod->id, 'applicant_id'=>$user->id]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $trade = Trade::factory()->create(['request_id'=>$request->id, 'bid_id'=>$bid->id]);
        $invoice = Invoice::factory()->create(['applicant_id'=>$user->id, 'trade_id'=>$trade->id, 'target_account_id'=>$linkedMethod->id]);
        $transactionMethod = TransactionMethod::factory()->create();
        $transaction = Transaction::factory()->create(['invoice_id'=>$invoice->id, 'transaction_method_id'=>$transactionMethod->id]);

        $this->assertInstanceOf(Invoice::class, $transaction->invoice);
    }
}
