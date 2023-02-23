<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\Bid;
use App\Models\Country;
use App\Models\Email;
use App\Models\EmailTemplate;
use App\Models\LinkedMethod;
use App\Models\PaymentMethod;
use App\Models\Request;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmailTest extends TestCase
{
    /*     use RefreshDatabase, WithFaker;
 */

    /** @test for the 1 to n User - Email relation*/
    public function an_email_belongs_to_a_user()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $emaiTemplate = EmailTemplate::factory()->create();
        $email = Email::factory()->create(['user_id'=> $user->id, 'template_id'=>$emaiTemplate->id, 'emailable_type' => "App\Models\User", 'emailable_id' => $user->id]);

        $this->assertInstanceOf(User::class, $email->user);
    }

    /** @test for the 1 to n polymorph Email - User relation*/
    public function an_email_can_be_morphed_to_a_user_model()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $emaiTemplate = EmailTemplate::factory()->create();
        $email = Email::factory()->create(['user_id'=>$user->id, 'template_id'=>$emaiTemplate->id, 'emailable_id' => $user->id, 'emailable_type' => "App\Models\User"]);

        $this->assertInstanceOf(User::class, $email->emailable);
    }

    /** @test for the 1 to n polymorph Email - Request relation*/
    public function an_email_can_be_morphed_to_a_request_model()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $emaiTemplate = EmailTemplate::factory()->create();
        $email = Email::factory()->create(['user_id'=>$user->id, 'template_id'=>$emaiTemplate->id, 'emailable_id' => $request->id, 'emailable_type' => "App\Models\Request"]);

        $this->assertInstanceOf(Request::class, $email->emailable);
    }

    /** @test for the 1 to n polymorph Email - Bid relation*/
    public function an_email_can_be_morphed_to_a_bid_model()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $emaiTemplate = EmailTemplate::factory()->create();
        $email = Email::factory()->create(['user_id'=>$user->id, 'template_id'=>$emaiTemplate->id, 'emailable_id' => $bid->id, 'emailable_type' => "App\Models\Bid"]);

        $this->assertInstanceOf(Bid::class, $email->emailable);
    }

    /** @test for the 1 to n polymorph Email - Trade relation*/
    public function an_email_can_be_morphed_to_a_trade_model()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $trade = Trade::factory()->create(['request_id'=>$request->id, 'bid_id'=>$bid->id]);
        $emaiTemplate = EmailTemplate::factory()->create();
        $email = Email::factory()->create(['user_id'=>$user->id, 'template_id'=>$emaiTemplate->id, 'emailable_id' => $trade->id, 'emailable_type' => "App\Models\Trade"]);

        $this->assertInstanceOf(Trade::class, $email->emailable);
    }
}
