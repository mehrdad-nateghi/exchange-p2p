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
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected $country;
    protected $paymentMethod;
    protected $user;
    protected $linkedMethod;
    protected $request;
    protected $bid;
    protected $trade;
    protected $invoice;
    protected $transactionMethod;
    protected $transaction;

    protected function setUp(): void
    {
        Parent::setUp();

        $this->country = Country::factory()->create();
        $this->paymentMethod = PaymentMethod::factory()->create(['country_id' => $this->country->id]);
        $this->user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $this->linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$this->paymentMethod->id, 'applicant_id'=>$this->user->id]);
        $this->request = Request::factory()->create(['applicant_id' => $this->user->id]);
        $this->bid = Bid::factory()->create(['applicant_id'=>$this->user->id, 'request_id'=>$this->request->id]);
        $this->trade = Trade::factory()->create(['request_id'=>$this->request->id, 'bid_id'=>$this->bid->id]);
        $this->invoice = Invoice::factory()->create(['applicant_id'=>$this->user->id, 'trade_id'=>$this->trade->id, 'target_account_id'=>$this->linkedMethod->id]);
        $this->transactionMethod = TransactionMethod::factory()->create();
        $this->transaction = Transaction::factory()->create(['invoice_id'=>$this->invoice->id, 'transaction_method_id'=>$this->transactionMethod->id]);
    }

    /** @test for the 1 to n User - Invoice relation*/
    public function a_invoice_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->invoice->user);
    }

    /** @test for the 1 to n LinkedMethod - Invoice relation*/
    public function a_invoice_belongs_to_a_linkedmethod()
    {
        $this->assertInstanceOf(LinkedMethod::class, $this->invoice->linkedMethod);
    }

    /** @test for the 1 to n Trade - Invoice relation*/
    public function a_invoice_belongs_to_a_trade()
    {
        $this->assertInstanceOf(Trade::class, $this->invoice->trade);
    }

    /** @test for the 1 to n Invoice - Transaction relation*/
    public function a_invoice_has_many_transactions()
    {
        $this->assertTrue($this->invoice->transactions->contains($this->transaction));
    }
}
