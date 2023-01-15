<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotificationTest extends TestCase
{
/*     use RefreshDatabase, WithFaker;
 */
    /** @test for the 1 to n User - Notification relation*/
    public function a_notification_belongs_to_a_user()
    {
        $user = User::factory()->create(['type'=>'1']);
        $notification = Notification::factory()->create(['user_id'=> $user->id]);

        $this->assertInstanceOf(User::class, $notification->user);
    }
}
