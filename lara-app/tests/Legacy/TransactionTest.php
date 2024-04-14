<?php

namespace Tests\Feature;

use App\Enums\UserRoleEnum;
use App\Models\Bid;
use App\Models\Country;
use App\Models\File;
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
    protected $file;

    protected function setUp(): void
    {
        Parent::setup();

        $this->country = Country::factory()->create();
        $this->paymentMethod = PaymentMethod::factory()->create(['country_id' => $this->country->id]);
        $this->user = User::factory()->create(['role'=>UserRoleEnum::Applicant]);
        $this->linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$this->paymentMethod->id, 'applicant_id'=>$this->user->id]);
        $this->request = Request::factory()->create(['applicant_id' => $this->user->id]);
        $this->bid = Bid::factory()->create(['applicant_id'=>$this->user->id, 'request_id'=>$this->request->id]);
        $this->trade = Trade::factory()->create(['request_id'=>$this->request->id, 'bid_id'=>$this->bid->id]);
        $this->invoice = Invoice::factory()->create(['applicant_id'=>$this->user->id, 'trade_id'=>$this->trade->id]);
        $this->transactionMethod = TransactionMethod::factory()->create();
        $this->transaction = Transaction::factory()->create(['invoice_id'=>$this->invoice->id, 'transaction_method_id'=>$this->transactionMethod->id]);
        $this->file = File::factory()->create(['transaction_id' => $this->transaction->id]);
    }

    /** @test for the 1 to n TransactionMethod - Transaction relation*/
    public function a_transaction_belongs_to_a_transactionmethod()
    {
        $this->assertInstanceOf(TransactionMethod::class, $this->transaction->transactionMethod);
    }

    /** @test for the 1 to n Invoice - Transaction relation*/
    public function a_transaction_belongs_to_a_invoice()
    {
        $this->assertInstanceOf(Invoice::class, $this->transaction->invoice);
    }

    /** @test for the 1 to 1 Transaction - File relation*/
    public function a_transaction_has_a_file()
    {
        $this->assertInstanceOf(File::class, $this->transaction->file);
    }
}
