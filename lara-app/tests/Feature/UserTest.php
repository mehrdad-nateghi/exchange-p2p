<?php

namespace Tests\Feature;

use App\Enums\UserRoleEnum;
use App\Models\AuthenticationLog;
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
use App\Models\UserVerify;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
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
    protected $userVerify;
    protected $authenticationLog;
    protected $emaiTemplate;
    protected $email;
    protected $notification;

    protected function setUp(): void
    {
        Parent::setup();

        $this->user = User::factory()->create(['role'=>UserRoleEnum::Applicant]);
        $this->userVerify = UserVerify::factory()->create(['user_id'=> $this->user->id]);
        $this->authenticationLog = AuthenticationLog::factory()->create(['applicant_id'=> $this->user->id]);
        $this->country = Country::factory()->create();
        $this->paymentMethod = PaymentMethod::factory()->create(['country_id' => $this->country->id]);
        $this->linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$this->paymentMethod->id, 'applicant_id'=>$this->user->id]);
        $this->request = Request::factory()->create(['applicant_id' => $this->user->id]);
        $this->bid = Bid::factory()->create(['applicant_id'=>$this->user->id, 'request_id'=>$this->request->id]);
        $this->trade = Trade::factory()->create(['request_id'=>$this->request->id, 'bid_id'=>$this->bid->id]);
        $this->invoice = Invoice::factory()->create(['applicant_id'=>$this->user->id, 'trade_id'=>$this->trade->id]);
        $this->emaiTemplate = EmailTemplate::factory()->create();
        $this->email = Email::factory()->create(['user_id'=> $this->user->id, 'template_id'=>$this->emaiTemplate->id, 'emailable_type' => "App\Models\User", 'emailable_id' => $this->user->id]);
        $this->notification = Notification::factory()->create(['user_id'=> $this->user->id]);
    }

    /** @test for the 1 to n User - Notification relation*/
    public function a_user_has_many_notifications()
    {
        $this->assertTrue($this->user->notifications->contains($this->notification));
    }

    /** @test for the 1 to 1 User - UserVerify relation*/
    public function a_user_has_a_userverify()
    {
        $this->assertInstanceOf(UserVerify::class, $this->user->userVerify);
    }

    /** @test for the 1 to n User - AuthenticationLog relation*/
    public function a_user_has_many_authenticationlogs()
    {
        $this->assertTrue($this->user->authenticationLogs->contains($this->authenticationLog));
    }

    /** @test for the 1 to n User - LinkedMethod relation*/
    public function a_user_has_many_linkedmethods()
    {
        $this->assertTrue($this->user->linkedMethods->contains($this->linkedMethod));
    }

    /** @test for the 1 to n User - Request relation*/
    public function a_user_has_many_requests()
    {
        $this->assertTrue($this->user->requests->contains($this->request));
    }

    /** @test for the 1 to n User - Bid relation*/
    public function a_user_has_many_bids()
    {
        $this->assertTrue($this->user->bids->contains($this->bid));
    }

    /** @test for the 1 to n User - Invoice relation*/
    public function a_user_has_many_invoices()
    {
        $this->assertTrue($this->user->invoices->contains($this->invoice));
    }

    /** @test for the 1 to n User - Email relation*/
    public function a_user_has_many_emails()
    {
        $this->assertTrue($this->user->emails->contains($this->email));
    }

    /** @test for the 1 to n polymorph Notification - User relation*/
    public function a_user_morphs_many_notifications(){
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->user->relatedNotifications);
    }

}
