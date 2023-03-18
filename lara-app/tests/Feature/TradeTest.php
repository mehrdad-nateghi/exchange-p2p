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
    use RefreshDatabase, WithFaker;

    /** @test for the 1 to n Request - Trade relation*/
    public function a_trade_belongs_to_a_request()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $trade = Trade::factory()->create(['request_id' => $request->id, 'bid_id' => $bid->id]);

        $this->assertInstanceOf(Request::class, $trade->request);
    }

    /** @test for the 1 to 1 Bid - Trade relation*/
    public function a_trade_belongs_to_a_bid()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $trade = Trade::factory()->create(['request_id' => $request->id, 'bid_id' => $bid->id]);

        $this->assertInstanceOf(Bid::class, $trade->bid);
    }

    /** @test for the 1 to n Trade - Invoice relation*/
    public function a_trade_has_many_invoices()
    {
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$paymentMethod->id, 'applicant_id'=>$user->id]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $trade = Trade::factory()->create(['request_id'=>$request->id, 'bid_id'=>$bid->id]);
        $invoice = Invoice::factory()->create(['applicant_id'=>$user->id, 'trade_id'=>$trade->id, 'target_account_id'=>$linkedMethod->id]);

        $this->assertTrue($trade->invoices->contains($invoice));
    }

    /** @test for the 1 to n polymorph Email - Trade relation*/
    public function a_trade_morphs_many_emails(){
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $trade = Trade::factory()->create(['request_id'=>$request->id, 'bid_id'=>$bid->id]);
        $emaiTemplate = EmailTemplate::factory()->create();
        $email = Email::factory()->create(['user_id'=>$user->id, 'template_id'=>$emaiTemplate->id, 'emailable_id' => $trade->id, 'emailable_type' => "App\Models\Trade"]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $trade->emails);
    }

    /** @test for the 1 to n polymorph Notification - Trade relation*/
    public function a_trade_morphs_many_notifications(){
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $trade = Trade::factory()->create(['request_id'=>$request->id, 'bid_id'=>$bid->id]);
        $notification = Notification::factory()->create(['user_id'=> $user->id, 'notifiable_id' => $trade->id, 'notifiable_type' => "App\Models\Trade"]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $trade->notifications);
    }
}
