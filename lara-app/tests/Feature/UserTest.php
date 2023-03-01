<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
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
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
/*     use RefreshDatabase, WithFaker; */

    /** @test for the 1 to n User - Notification relation*/
    public function a_user_has_many_notifications()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $notification = Notification::factory()->create(['user_id'=> $user->id]);

        $this->assertTrue($user->notifications->contains($notification));
    }

    /** @test for the 1 to 1 User - UserVerify relation*/
    public function a_user_has_a_userverify()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $userVerify = UserVerify::factory()->create(['user_id'=> $user->id]);

        $this->assertInstanceOf(UserVerify::class, $user->userVerify);
    }

    /** @test for the 1 to n User - AuthenticationLog relation*/
    public function a_user_has_many_authenticationlogs()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $authenticationLog = AuthenticationLog::factory()->create(['applicant_id'=> $user->id]);

        $this->assertTrue($user->authenticationLogs->contains($authenticationLog));
    }

    /** @test for the 1 to n User - LinkedMethod relation*/
    public function a_user_has_many_linkedmethods()
    {
        $country = Country::factory()->create();
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$paymentMethod->id, 'applicant_id'=>$user->id]);

        $this->assertTrue($user->linkedMethods->contains($linkedMethod));
    }

    /** @test for the 1 to n User - Request relation*/
    public function a_user_has_many_requests()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);

        $this->assertTrue($user->requests->contains($request));
    }

    /** @test for the 1 to n User - Bid relation*/
    public function a_user_has_many_bids()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);

        $this->assertTrue($user->bids->contains($bid));
    }

    /** @test for the 1 to n User - Invoice relation*/
    public function a_user_has_many_invoices()
    {
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$paymentMethod->id, 'applicant_id'=>$user->id]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $trade = Trade::factory()->create(['request_id'=>$request->id, 'bid_id'=>$bid->id]);
        $invoice = Invoice::factory()->create(['applicant_id'=>$user->id, 'trade_id'=>$trade->id, 'target_account_id'=>$linkedMethod->id]);

        $this->assertTrue($user->invoices->contains($invoice));
    }

    /** @test for the 1 to n User - Email relation*/
    public function a_user_has_many_emails()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $emaiTemplate = EmailTemplate::factory()->create();
        $email = Email::factory()->create(['user_id'=> $user->id, 'template_id'=>$emaiTemplate->id, 'emailable_type' => "App\Models\User", 'emailable_id' => $user->id]);

        $this->assertTrue($user->emails->contains($email));
    }

    /** @test for the 1 to n polymorph Notification - User relation*/
    public function a_user_morphs_many_notifications(){
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $notification = Notification::factory()->create(['user_id'=> $user->id, 'notifiable_id' => $user->id, 'notifiable_type' => "App\Models\User"]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->relatedNotifications);
    }

}
