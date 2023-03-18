<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\Bid;
use App\Models\Notification;
use App\Models\Request;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test for the 1 to n User - Notification relation*/
    public function a_notification_belongs_to_a_user()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $notification = Notification::factory()->create(['user_id'=> $user->id]);

        $this->assertInstanceOf(User::class, $notification->user);
    }

    /** @test for the 1 to n polymorph Notification - Request relation*/
    public function a_notification_can_be_morphed_to_a_request_model()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $notification = Notification::factory()->create(['user_id'=> $user->id, 'notifiable_id' => $request->id, 'notifiable_type' => "App\Models\Request"]);

        $this->assertInstanceOf(Request::class, $notification->notifiable);
    }

    /** @test for the 1 to n polymorph Notification - User relation*/
    public function a_notification_can_be_morphed_to_a_user_model()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $notification = Notification::factory()->create(['user_id'=> $user->id, 'notifiable_id' => $user->id, 'notifiable_type' => "App\Models\User"]);

        $this->assertInstanceOf(User::class, $notification->notifiable);
    }

    /** @test for the 1 to n polymorph Notification - Bid relation*/
    public function a_notification_can_be_morphed_to_a_bid_model()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $notification = Notification::factory()->create(['user_id'=> $user->id, 'notifiable_id' => $bid->id, 'notifiable_type' => "App\Models\Bid"]);

        $this->assertInstanceOf(Bid::class, $notification->notifiable);
    }

    /** @test for the 1 to n polymorph Notification - Trade relation*/
    public function a_notification_can_be_morphed_to_a_trade_model()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $trade = Trade::factory()->create(['request_id'=>$request->id, 'bid_id'=>$bid->id]);
        $notification = Notification::factory()->create(['user_id'=> $user->id, 'notifiable_id' => $trade->id, 'notifiable_type' => "App\Models\Trade"]);

        $this->assertInstanceOf(Trade::class, $notification->notifiable);
    }

}
