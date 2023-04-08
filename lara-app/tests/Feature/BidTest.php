<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\Bid;
use App\Models\Email;
use App\Models\EmailTemplate;
use App\Models\Notification;
use App\Models\Request;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BidTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $request;
    protected $bid;
    protected $trade;
    protected $emaiTemplate;
    protected $email;
    protected $notification;


    protected function setUp() :void
    {
        parent::setUp();

        $this->user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $this->request = Request::factory()->create(['applicant_id' => $this->user->id]);
        $this->bid = Bid::factory()->create(['applicant_id'=>$this->user->id, 'request_id'=>$this->request->id]);
        $this->trade = Trade::factory()->create(['request_id' => $this->request->id, 'bid_id' => $this->bid->id]);
        $this->emaiTemplate = EmailTemplate::factory()->create();
        $this->email = Email::factory()->create(['user_id'=>$this->user->id, 'template_id'=>$this->emaiTemplate->id, 'emailable_id' => $this->bid->id, 'emailable_type' => "App\Models\Bid"]);
        $this->notification = Notification::factory()->create(['user_id'=> $this->user->id, 'notifiable_id' => $this->bid->id, 'notifiable_type' => "App\Models\Bid"]);
    }

    /** @test for the 1 to n User - Bid relation*/
    public function a_bid_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->bid->user);
    }

    /** @test for the 1 to n Request - Bid relation*/
    public function a_bid_belongs_to_a_request()
    {
        $this->assertInstanceOf(Request::class, $this->bid->request);
    }

    /** @test for the 1 to 1 Bid - Trade relation*/
    public function a_bid_has_a_trade()
    {
        $this->assertInstanceOf(Trade::class, $this->bid->trade);
    }

    /** @test for the 1 to n polymorph Email - Bid relation*/
    public function a_bid_morphs_many_emails()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->bid->emails);
    }

    /** @test for the 1 to n polymorph Notification - Bid relation*/
    public function a_bid_morphs_many_notifications()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->bid->notifications);
    }
}
