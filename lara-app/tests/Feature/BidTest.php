<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\Bid;
use App\Models\Email;
use App\Models\EmailTemplate;
use App\Models\Request;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BidTest extends TestCase
{
/*     use RefreshDatabase, WithFaker;
 */
    /** @test for the 1 to n User - Bid relation*/
    public function a_bid_belongs_to_a_user()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);

        $this->assertInstanceOf(User::class, $bid->user);
    }

    /** @test for the 1 to n Request - Bid relation*/
    public function a_bid_belongs_to_a_request()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);

        $this->assertInstanceOf(Request::class, $bid->request);
    }

    /** @test for the 1 to 1 Bid - Trade relation*/
    public function a_bid_has_a_trade()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $trade = Trade::factory()->create(['request_id' => $request->id, 'bid_id' => $bid->id]);

        $this->assertInstanceOf(Trade::class, $bid->trade);
    }

    /** @test for the 1 to n polymorph Email - Bid relation*/
    public function a_bid_morphs_many_emails(){
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $emaiTemplate = EmailTemplate::factory()->create();
        $email = Email::factory()->create(['user_id'=>$user->id, 'template_id'=>$emaiTemplate->id, 'emailable_id' => $bid->id, 'emailable_type' => "App\Models\Bid"]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $bid->emails);
    }
}
