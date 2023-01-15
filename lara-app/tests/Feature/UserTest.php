<?php

namespace Tests\Feature;

use App\Models\AuthenticationLog;
use App\Models\Notification;
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
        $user = User::factory()->create(['type'=>'1']);
        $notification = Notification::factory()->create(['user_id'=> $user->id]);

        $this->assertTrue($user->notifications->contains($notification));
    }

    /** @test for the 1 to 1 User - UserVerify relation*/
    public function a_user_has_a_userverify()
    {
        $user = User::factory()->create(['type'=>'1']);
        $userVerify = UserVerify::factory()->create(['user_id'=> $user->id]);

        $this->assertInstanceOf(UserVerify::class, $user->userVerify);
    }

    /** @test for the 1 to n User - AuthenticationLog relation*/
    public function a_user_has_many_authenticationlogs()
    {
        $user = User::factory()->create(['type'=>'1']);
        $authenticationLog = AuthenticationLog::factory()->create(['applicant_id'=> $user->id]);

        $this->assertTrue($user->authenticationLogs->contains($authenticationLog));
    }

}
