<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\Bid;
use App\Models\Notification;
use App\Models\Request;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $request;
    protected $bid;
    protected $trade;
    protected $notification;

    protected function setUp(): void
    {
        Parent::setUp();

        $this->user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $this->request = Request::factory()->create(['applicant_id' => $this->user->id]);
        $this->bid = Bid::factory()->create(['applicant_id'=>$this->user->id, 'request_id'=>$this->request->id]);
        $this->trade = Trade::factory()->create(['request_id'=>$this->request->id, 'bid_id'=>$this->bid->id]);
        $this->notification = Notification::factory()->create(['user_id'=> $this->user->id, 'notifiable_id' => $this->trade->id, 'notifiable_type' => "App\Models\Trade"]);
    }

    /** @test for the 1 to n User - Notification relation*/
    public function a_notification_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->notification->user);
    }

    /** @test for the 1 to n polymorph Notification - Request relation*/
    public function a_notification_can_be_morphed_to_a_request_model()
    {
        $this->notification = Notification::factory()->create(['user_id'=> $this->user->id, 'notifiable_id' => $this->request->id, 'notifiable_type' => "App\Models\Request"]);

        $this->assertInstanceOf(Request::class, $this->notification->notifiable);
    }

    /** @test for the 1 to n polymorph Notification - User relation*/
    public function a_notification_can_be_morphed_to_a_user_model()
    {
        $this->notification = Notification::factory()->create(['user_id'=> $this->user->id, 'notifiable_id' => $this->user->id, 'notifiable_type' => "App\Models\User"]);

        $this->assertInstanceOf(User::class, $this->notification->notifiable);
    }

    /** @test for the 1 to n polymorph Notification - Bid relation*/
    public function a_notification_can_be_morphed_to_a_bid_model()
    {
        $this->notification = Notification::factory()->create(['user_id'=> $this->user->id, 'notifiable_id' => $this->bid->id, 'notifiable_type' => "App\Models\Bid"]);

        $this->assertInstanceOf(Bid::class, $this->notification->notifiable);
    }

    /** @test for the 1 to n polymorph Notification - Trade relation*/
    public function a_notification_can_be_morphed_to_a_trade_model()
    {
        $this->notification = Notification::factory()->create(['user_id'=> $this->user->id, 'notifiable_id' => $this->trade->id, 'notifiable_type' => "App\Models\Trade"]);

        $this->assertInstanceOf(Trade::class, $this->notification->notifiable);
    }

}
