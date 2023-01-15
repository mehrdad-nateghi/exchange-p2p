<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserVerify;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserVerifyTest extends TestCase
{
    /* use RefreshDatabase, WithFaker; */

    /** @test for the 1 to 1 User - UserVerify relation*/
    public function a_userverify_belongs_to_a_user()
    {
        $user = User::factory()->create(['type'=>'1']);
        $userVerify = UserVerify::factory()->create(['user_id'=> $user->id]);

        $this->assertInstanceOf(User::class, $userVerify->user);
    }
}
