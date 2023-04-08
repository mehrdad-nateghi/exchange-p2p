<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\Bid;
use App\Models\Country;
use App\Models\Email;
use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Models\LinkedMethod;
use App\Models\Notification;
use App\Models\PaymentMethod;
use App\Models\Request;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TradeTest extends TestCase
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
    protected $notification;
    protected $emailTemplate;
    protected $email;

    protected function setUp(): void
    {
        Parent::setup();

        $this->country = Country::factory()->create();
        $this->paymentMethod = PaymentMethod::factory()->create(['country_id' => $this->country->id]);
        $this->user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $this->linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$this->paymentMethod->id, 'applicant_id'=>$this->user->id]);
        $this->request = Request::factory()->create(['applicant_id' => $this->user->id]);
        $this->bid = Bid::factory()->create(['applicant_id'=>$this->user->id, 'request_id'=>$this->request->id]);
        $this->trade = Trade::factory()->create(['request_id'=>$this->request->id, 'bid_id'=>$this->bid->id]);
        $this->invoice = Invoice::factory()->create(['applicant_id'=>$this->user->id, 'trade_id'=>$this->trade->id, 'target_account_id'=>$this->linkedMethod->id]);
        $this->notification = Notification::factory()->create(['user_id'=> $this->user->id, 'notifiable_id' => $this->trade->id, 'notifiable_type' => "App\Models\Trade"]);
        $this->emailTemplate = EmailTemplate::factory()->create();
        $this->email = Email::factory()->create(['user_id'=>$this->user->id, 'template_id'=>$this->emailTemplate->id, 'emailable_id' => $this->trade->id, 'emailable_type' => "App\Models\Trade"]);
    }

    /** @test for the 1 to n Request - Trade relation*/
    public function a_trade_belongs_to_a_request()
    {
        $this->assertInstanceOf(Request::class, $this->trade->request);
    }

    /** @test for the 1 to 1 Bid - Trade relation*/
    public function a_trade_belongs_to_a_bid()
    {
        $this->assertInstanceOf(Bid::class, $this->trade->bid);
    }

    /** @test for the 1 to n Trade - Invoice relation*/
    public function a_trade_has_many_invoices()
    {
        $this->assertTrue($this->trade->invoices->contains($this->invoice));
    }

    /** @test for the 1 to n polymorph Email - Trade relation*/
    public function a_trade_morphs_many_emails()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->trade->emails);
    }

    /** @test for the 1 to n polymorph Notification - Trade relation*/
    public function a_trade_morphs_many_notifications()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->trade->notifications);
    }
}
