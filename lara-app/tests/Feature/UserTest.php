<?php

namespace Tests\Feature;

use App\Models\AuthenticationLog;
use App\Models\Country;
use App\Models\LinkedMethod;
use App\Models\Notification;
use App\Models\PaymentMethod;
use App\Models\Request;
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

    /** @test for the 1 to n User - LinkedMethod relation*/
    public function a_user_has_many_linkedmethods()
    {
        $country = Country::factory()->create();
        $user = User::factory()->create(['type'=>'1']);
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$paymentMethod->id, 'applicant_id'=>$user->id]);

        $this->assertTrue($user->linkedMethods->contains($linkedMethod));
    }

    /** @test for the 1 to n User - Request relation*/
    public function a_user_has_many_requests()
    {
        $user = User::factory()->create(['type'=>'1']);
        $request = Request::factory()->create(['applicant_id' => $user->id]);

        $this->assertTrue($user->requests->contains($request));
    }
}
